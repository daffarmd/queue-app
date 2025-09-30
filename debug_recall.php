<?php

require_once __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Queue;
use App\Services\QueueService;

echo "=== DEBUG RECALL QUEUE ===\n";

// Find a queue to test with
$queue = Queue::find(17);
if (! $queue) {
    echo "Queue not found!\n";
    exit;
}

echo "Original queue status: {$queue->status}\n";

// Set it to skipped first
$queue->update(['status' => 'skipped']);
echo "Set to skipped: {$queue->fresh()->status}\n";

// Now test the service
$queueService = app(QueueService::class);
echo 'QueueService class: '.get_class($queueService)."\n";

// Use reflection to see the actual method
$reflection = new ReflectionClass($queueService);
$method = $reflection->getMethod('recallQueue');
echo 'Method file: '.$method->getFileName()."\n";
echo 'Method start line: '.$method->getStartLine()."\n";

// Call the method
$result = $queueService->recallQueue($queue, '10');
echo "Result status: {$result->status}\n";
echo "Fresh status: {$result->fresh()->status}\n";

// Check if there are any issues
$fresh = Queue::find(17);
echo "Direct DB query status: {$fresh->status}\n";
