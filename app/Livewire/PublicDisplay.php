<?php

namespace App\Livewire;

use App\Models\Queue;
use Carbon\Carbon;
use Livewire\Component;

class PublicDisplay extends Component
{
    public ?Queue $currentQueue = null;

    public $waitingQueues;

    public $calledQueues;

    public $recalledQueues;

    protected $listeners = [
        'queueCalled' => 'handleQueueCalled',
        'queueCreated' => 'refreshDisplay',
        'queueRecalled' => 'handleQueueRecalled',
        'checkForNewCalls' => 'checkForNewCalls',
    ];

    public function mount()
    {
        // Initialize collections
        $this->waitingQueues = collect();
        $this->calledQueues = collect();
        $this->recalledQueues = collect();

        $this->refreshDisplay();
    }

    public function handleQueueCalled($queueData)
    {
        $this->refreshDisplay();

        // Trigger TTS announcement with Piper
        $this->dispatch('announceTTS', [
            'code' => $queueData['code'],
            'service' => $queueData['service_name'] ?? '',
            'destination' => $queueData['destination_name'] ?? '',
            'type' => 'called',
            'fallback_message' => "Queue {$queueData['code']}, to {$queueData['destination_name']}, please come to your destination",
        ]);
    }

    public function handleQueueRecalled($queueData)
    {
        $this->refreshDisplay();

        // Trigger TTS announcement for recalled queue
        $this->dispatch('announceTTS', [
            'code' => $queueData['code'],
            'service' => $queueData['service_name'] ?? '',
            'destination' => $queueData['destination_name'] ?? '',
            'type' => 'recalled',
            'fallback_message' => "Queue {$queueData['code']}, to {$queueData['destination_name']}, please return to your destination",
        ]);
    }

    // Add a method to check for new calls and trigger announcements
    public function checkForNewCalls()
    {
        $previousQueue = $this->currentQueue;
        $this->refreshDisplay();

        // If there's a new current queue, announce it
        if ($this->currentQueue && (! $previousQueue || $this->currentQueue->id !== $previousQueue->id)) {
            $destinationName = $this->currentQueue->destination?->name ?? 'your destination';
            $serviceName = $this->currentQueue->service?->name ?? '';

            $this->dispatch('announceTTS', [
                'code' => $this->currentQueue->code,
                'service' => $serviceName,
                'destination' => $destinationName,
                'type' => $this->currentQueue->status === 'recalled' ? 'recalled' : 'called',
                'fallback_message' => "Queue {$this->currentQueue->code}, to {$destinationName}, please come to your destination",
            ]);
        }
    }

    public function refreshDisplay()
    {
        $todayWIB = Carbon::now('Asia/Jakarta')->startOfDay();

        // Get current queue (most recent called or recalled queue)
        // For UI purposes, recalled queues should appear as called
        $this->currentQueue = Queue::with(['service', 'destination'])
            ->whereIn('status', ['called', 'recalled'])
            ->whereDate('created_at', today())
            ->orderBy('called_at', 'desc')
            ->first();

        // Get waiting queues
        $this->waitingQueues = Queue::with(['service', 'destination'])
            ->where('status', 'waiting')
            ->whereDate('created_at', today())
            ->orderBy('created_at')
            ->take(10)
            ->get();

        // Remove calledQueues and recalledQueues collections
        $this->calledQueues = collect();
        $this->recalledQueues = collect();
    }

    // Auto-refresh every 30 seconds
    public function refreshData()
    {
        $this->refreshDisplay();
    }

    public function render()
    {
        return view('livewire.public-display');
    }
}
