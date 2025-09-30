<?php

namespace App\Livewire;

use App\Models\Queue;
use App\Models\Service;
use App\Services\PrinterService;
use App\Services\QueueService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class StaffDashboard extends Component
{
    protected string $layout = 'components.layouts.app';

    public $services;
    public $selectedService = null;

    public $patientName = '';

    public $counter = '1';

    protected $listeners = [
        'queueCreated' => 'refreshQueues',
        'queueCalled' => 'refreshQueues',
        'queueRecalled' => 'refreshQueues',
    ];

    protected $rules = [
        'selectedService' => 'required|exists:services,id',
        'patientName' => 'required|string|max:255|regex:/^[a-zA-Z\s\.\-\']+$/u',
        'counter' => 'required|string|max:10|alpha_num',
    ];

    protected $messages = [
        'selectedService.required' => 'Please select a service.',
        'patientName.required' => 'Patient name is required.',
        'patientName.regex' => 'Patient name contains invalid characters.',
        'counter.required' => 'Counter number is required.',
    ];

    public function mount()
    {
        // Check authorization
        if (! Auth::check() || ! Auth::user()->hasAnyRole(['Admin', 'Staff'])) {
            abort(403, 'Unauthorized access');
        }

        $this->services = Service::all();
    }

    public function createQueue()
    {
        $this->validate();

        try {
            $queue = app(QueueService::class)->createQueue(
                $this->selectedService,
                $this->patientName
            );

            // Attempt to print ticket
            $printed = app(PrinterService::class)->printQueueTicket($queue);

            if (! $printed) {
                session()->flash('warning', 'Queue created but printing failed: '.$queue->code);
            } else {
                session()->flash('success', 'Queue created and printed: '.$queue->code);
            }

            $this->reset(['selectedService', 'patientName']);
            $this->refreshQueues();

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

            app(QueueService::class)->callQueue($queue, $this->counter);

            session()->flash('success', 'Queue '.$queue->code.' called successfully');
            $this->refreshQueues();

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
            $this->refreshQueues();

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
            $this->refreshQueues();

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

            app(QueueService::class)->recallQueue($queue, $this->counter);

            session()->flash('success', 'Queue '.$queue->code.' recalled successfully');
            $this->refreshQueues();

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to recall queue: '.$e->getMessage());
        }
    }

    public function getQueuesProperty()
    {
        $todayWIB = Carbon::now('Asia/Jakarta')->startOfDay();

        return Queue::with('service')
            ->where('created_at', '>=', $todayWIB)
            ->orderBy('created_at')
            ->get()
            ->groupBy('status');
    }

    public function refreshQueues()
    {
        // This method can stay for event listeners to trigger re-rendering
        // The actual queues will be computed via the getQueuesProperty method
    }

    public function render()
    {
        return view('livewire.staff-dashboard');
    }
}
