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

    $queue = $this->queueService->createQueue($service->id, 'John Doe');

    expect($queue)->toBeInstanceOf(Queue::class);
    expect($queue->patient_name)->toBe('John Doe');
    expect($queue->code)->toBe('GEN-001');
    expect($queue->number)->toBe(1);
    expect($queue->status)->toBe('waiting');
});

test('queue numbering increments per service', function () {
    $service = Service::factory()->create(['code' => 'GEN']);

    $queue1 = $this->queueService->createQueue($service->id, 'Patient 1');
    $queue2 = $this->queueService->createQueue($service->id, 'Patient 2');

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
    $queue = $this->queueService->createQueue($service->id, 'Test Patient');

    expect($queue->number)->toBe(1);
    expect($queue->code)->toBe('GEN-001');
});

test('can call queue', function () {
    $service = Service::factory()->create();
    $queue = Queue::factory()->create([
        'service_id' => $service->id,
        'status' => 'waiting',
    ]);

    $updatedQueue = $this->queueService->callQueue($queue, '1');

    expect($updatedQueue->status)->toBe('called');
    expect($updatedQueue->counter)->toBe('1');
    expect($updatedQueue->called_at)->not->toBeNull();
});

test('can recall skipped queue', function () {
    $service = Service::factory()->create();
    $queue = Queue::factory()->create([
        'service_id' => $service->id,
        'status' => 'skipped',
    ]);

    $updatedQueue = $this->queueService->recallQueue($queue, '2');

    expect($updatedQueue->status)->toBe('recalled');
    expect($updatedQueue->counter)->toBe('2');
    expect($updatedQueue->called_at)->not->toBeNull();
    expect($updatedQueue->finished_at)->toBeNull();
});
