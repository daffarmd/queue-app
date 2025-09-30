<?php

namespace App\Services;

use App\Events\QueueCalled;
use App\Events\QueueCreated;
use App\Events\QueueRecalled;
use App\Models\Queue;
use App\Models\Service;
use Carbon\Carbon;

class QueueService
{
    public function createQueue(int $serviceId, string $patientName): Queue
    {
        $service = Service::findOrFail($serviceId);

        // Get next number for today (WIB timezone GMT+7)
        $todayWIB = Carbon::now('Asia/Jakarta')->startOfDay();
        $lastNumber = Queue::where('service_id', $serviceId)
            ->where('created_at', '>=', $todayWIB)
            ->max('number') ?? 0;

        $nextNumber = $lastNumber + 1;
        $code = $service->code.'-'.str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        $queue = Queue::create([
            'service_id' => $serviceId,
            'patient_name' => $patientName,
            'number' => $nextNumber,
            'code' => $code,
            'status' => 'waiting',
        ]);

        event(new QueueCreated($queue));

        return $queue;
    }

    public function callQueue(Queue $queue, string $counter): Queue
    {
        $queue->update([
            'status' => 'called',
            'counter' => $counter,
            'called_at' => now(),
        ]);

        event(new QueueCalled($queue));

        return $queue;
    }

    public function finishQueue(Queue $queue): Queue
    {
        $queue->update([
            'status' => 'done',
            'finished_at' => now(),
        ]);

        return $queue;
    }

    public function skipQueue(Queue $queue): Queue
    {
        $queue->update([
            'status' => 'skipped',
            'finished_at' => now(),
        ]);

        return $queue;
    }

    public function recallQueue(Queue $queue, string $counter): Queue
    {
        $queue->update([
            'status' => 'recalled',
            'counter' => $counter,
            'called_at' => now(),
            'finished_at' => null,
        ]);

        event(new QueueRecalled($queue));

        return $queue;
    }

    public function getTodayQueues(?int $serviceId = null)
    {
        $todayWIB = Carbon::now('Asia/Jakarta')->startOfDay();

        $query = Queue::with('service')
            ->where('created_at', '>=', $todayWIB);

        if ($serviceId) {
            $query->where('service_id', $serviceId);
        }

        return $query->orderBy('created_at')->get();
    }
}
