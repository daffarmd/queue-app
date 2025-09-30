<?php

use App\Models\Queue;
use App\Models\Service;
use App\Services\QueueService;
use Carbon\Carbon;

beforeEach(function () {
    $this->queueService = app(QueueService::class);
});

test('can create queue', function () {
    $service = Service::factory()->create(['code' => 'GEN']);
    $destination = \App\Models\Destination::factory()->create();

    $queue = $this->queueService->createQueue($service->id, $destination->id);

    expect($queue)->toBeInstanceOf(Queue::class);
    expect($queue->destination_id)->toBe($destination->id);
    expect($queue->code)->toBe('GEN-001');
    expect($queue->number)->toBe(1);
    expect($queue->status)->toBe('waiting');
});

test('queue numbering increments per service', function () {
    $service = Service::factory()->create(['code' => 'GEN']);
    $destination1 = \App\Models\Destination::factory()->create();
    $destination2 = \App\Models\Destination::factory()->create();

    $queue1 = $this->queueService->createQueue($service->id, $destination1->id);
    $queue2 = $this->queueService->createQueue($service->id, $destination2->id);

    expect($queue1->code)->toBe('GEN-001');
    expect($queue2->code)->toBe('GEN-002');
    expect($queue1->number)->toBe(1);
    expect($queue2->number)->toBe(2);
});

test('queue numbering resets daily', function () {
    $service = Service::factory()->create(['code' => 'GEN']);

    // Create queue yesterday
    Queue::factory()->create([
        'service_id' => $service->id,
        'number' => 5,
        'code' => 'GEN-005',
        'created_at' => Carbon::now('Asia/Jakarta')->subDay(),
    ]);

    // Create queue today - should start from 1
    $destination = \App\Models\Destination::factory()->create();
    $queue = $this->queueService->createQueue($service->id, $destination->id);

    expect($queue->number)->toBe(1);
    expect($queue->code)->toBe('GEN-001');
});

test('can call queue', function () {
    $service = Service::factory()->create();
    $queue = Queue::factory()->create([
        'service_id' => $service->id,
        'status' => 'waiting',
    ]);

    $updatedQueue = $this->queueService->callQueue($queue);

    expect($updatedQueue->status)->toBe('called');
    expect($updatedQueue->called_at)->not->toBeNull();
});

test('can recall skipped queue', function () {
    $service = Service::factory()->create();
    $queue = Queue::factory()->create([
        'service_id' => $service->id,
        'status' => 'skipped',
    ]);

    $updatedQueue = $this->queueService->recallQueue($queue);

    expect($updatedQueue->status)->toBe('recalled');
    expect($updatedQueue->called_at)->not->toBeNull();
    expect($updatedQueue->finished_at)->toBeNull();
});
