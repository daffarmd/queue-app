<?php

namespace App\Livewire;

use App\Models\Destination;
use App\Models\Queue;
use App\Models\Service;
use App\Services\PrinterService;
use App\Services\QueueService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class StaffDashboard extends Component
{
    protected string $layout = 'components.layouts.app';

    public $selectedService = null;

    public $selectedDestination = null;

    public $customTTSMessage = 'Nomor antrian {queue_code}. Layanan {service_name}. Silahkan Menuju {destination_name}. Terimakasih.';

    protected $listeners = [
        'queueCreated' => '$refresh',
        'queueCalled' => '$refresh',
        'queueRecalled' => '$refresh',
    ];

    protected $rules = [
        'selectedService' => 'required|exists:services,id',
        'selectedDestination' => 'required|exists:destinations,id',
        'customTTSMessage' => 'nullable|string|max:500',
    ];

    protected $messages = [
        'selectedService.required' => 'Please select a service.',
        'selectedDestination.required' => 'Please select a destination.',
        'customTTSMessage.max' => 'TTS message cannot exceed 500 characters.',
    ];

    public function getServicesProperty()
    {
        return Service::all();
    }

    public function getDestinationsProperty()
    {
        return Destination::all();
    }

    public function getQueuesProperty()
    {
        $todayWIB = Carbon::now('Asia/Jakarta')->startOfDay();

        $allQueues = Queue::with(['service', 'destination'])
            ->where('created_at', '>=', $todayWIB)
            ->orderBy('created_at')
            ->get();

        $waiting = $allQueues->where('status', 'waiting')->values();
        $called = $allQueues->whereIn('status', ['called', 'recalled'])
            ->sortByDesc(function ($q) {
                return $q->called_at ? $q->called_at->timestamp : $q->created_at->timestamp;
            })
            ->values();
        $skipped = $allQueues->where('status', 'skipped')->values();

        return [
            'waiting' => $waiting,
            'called' => $called,
            'skipped' => $skipped,
        ];
    }

    public function mount()
    {
        // Check authorization
        if (! Auth::check() || ! Auth::user()->hasAnyRole(['Admin', 'Staff'])) {
            abort(403, 'Unauthorized access');
        }

        // Load persisted custom TTS message if exists
        $cached = Cache::get('custom_tts_message');
        if ($cached) {
            $this->customTTSMessage = $cached;
        }
    }

    public function createQueue()
    {
        $this->validate();

        try {
            $queue = app(QueueService::class)->createQueue(
                $this->selectedService,
                $this->selectedDestination
            );

            // Attempt to print ticket
            $printed = app(PrinterService::class)->printQueueTicket($queue);

            if (! $printed) {
                session()->flash('warning', 'Queue created but printing failed: '.$queue->code);
            } else {
                session()->flash('success', 'Queue created and printed: '.$queue->code);
            }

            $this->reset(['selectedService', 'selectedDestination']);

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to create queue: '.$e->getMessage());
        }
    }

    public function callQueue($queueId)
    {
        try {
            $queue = Queue::findOrFail($queueId);

            if (! in_array($queue->status, ['waiting', 'skipped'])) {
                session()->flash('error', 'Queue cannot be called in current status');

                return;
            }

            app(QueueService::class)->callQueue($queue);

            session()->flash('success', 'Queue '.$queue->code.' called successfully');

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to call queue: '.$e->getMessage());
        }
    }

    public function finishQueue($queueId)
    {
        try {
            $queue = Queue::findOrFail($queueId);
            app(QueueService::class)->finishQueue($queue);

            session()->flash('success', 'Queue '.$queue->code.' finished');

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to finish queue: '.$e->getMessage());
        }
    }

    public function skipQueue($queueId)
    {
        try {
            $queue = Queue::findOrFail($queueId);
            app(QueueService::class)->skipQueue($queue);

            session()->flash('info', 'Queue '.$queue->code.' skipped');

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to skip queue: '.$e->getMessage());
        }
    }





    public function recallQueue($queueId)
    {
        try {
            $queue = Queue::findOrFail($queueId);

            if ($queue->status !== 'skipped') {
                session()->flash('error', 'Only skipped queues can be recalled');

                return;
            }

            app(QueueService::class)->recallQueue($queue);

            session()->flash('success', 'Queue '.$queue->code.' recalled successfully');

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to recall queue: '.$e->getMessage());
        }
    }

    // Removed handleTTS indirection; front-end dispatch goes directly to browser TTS script

    /**
     * Persist custom TTS message whenever it changes so other displays can use latest template.
     */
    public function updatedCustomTTSMessage($value)
    {
        if (is_string($value) && strlen($value) <= 500) {
            Cache::put('custom_tts_message', $value, now()->addDay());
        }
    }

    public function render()
    {
        return view('livewire.staff-dashboard');
    }
}
