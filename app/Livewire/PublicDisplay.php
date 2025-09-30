<?php

namespace App\Livewire;

use App\Models\Queue;
use Carbon\Carbon;
use Livewire\Component;

class PublicDisplay extends Component
{
    public ?Queue $currentQueue = null;

    public $waitingQueues = [];

    public $calledQueues = [];

    public $recalledQueues = [];

    protected $listeners = [
        'queueCalled' => 'handleQueueCalled',
        'queueCreated' => 'refreshDisplay',
        'queueRecalled' => 'handleQueueRecalled',
    ];

    public function mount()
    {
        $this->refreshDisplay();
    }

    public function handleQueueCalled($queueData)
    {
        $this->refreshDisplay();

        // Trigger voice announcement
        $this->dispatch('announceQueue', [
            'message' => "Queue {$queueData['code']}, {$queueData['patient_name']}, please come to counter {$queueData['counter']}",
        ]);
    }

    public function handleQueueRecalled($queueData)
    {
        $this->refreshDisplay();

        // Trigger voice announcement for recalled queue
        $this->dispatch('announceQueue', [
            'message' => "Queue {$queueData['code']}, {$queueData['patient_name']}, please return to counter {$queueData['counter']}",
        ]);
    }

    public function refreshDisplay()
    {
        $todayWIB = Carbon::now('Asia/Jakarta')->startOfDay();

        // Get called queues (recently called)
        $calledQueuesCollection = Queue::with('service')
            ->where('status', 'called')
            ->whereDate('created_at', today())
            ->orderBy('called_at', 'desc')
            ->take(5)
            ->get();

        $this->calledQueues = $calledQueuesCollection->toArray();

        // Get the most recent called queue as current
        $this->currentQueue = $calledQueuesCollection->first();

        // Get recalled queues
        $this->recalledQueues = Queue::with('service')
            ->where('status', 'recalled')
            ->whereDate('created_at', today())
            ->orderBy('called_at', 'desc')
            ->take(3)
            ->get();

        // Get waiting queues
        $this->waitingQueues = Queue::with('service')
            ->where('status', 'waiting')
            ->whereDate('created_at', today())
            ->orderBy('created_at')
            ->take(10)
            ->get();
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
