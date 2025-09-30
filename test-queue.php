<?php

use App\Models\Service;
use App\Services\QueueService;

// Find or create a service
$service = Service::first();
if (! $service) {
    $service = Service::create([
        'name' => 'General Consultation',
        'code' => 'GEN',
        'description' => 'General medical consultation',
    ]);
    echo "Service created: {$service->code} - {$service->name}\n";
} else {
    echo "Using existing service: {$service->code} - {$service->name}\n";
}

// Create a queue
$queueService = app(QueueService::class);
$queue = $queueService->createQueue($service->id, 'John Doe Test');

echo "SUCCESS: Queue {$queue->number} created for {$service->name}\n";
echo "Patient: {$queue->patient_name}\n";
echo "Status: {$queue->status}\n";
echo "Created at: {$queue->created_at}\n";
