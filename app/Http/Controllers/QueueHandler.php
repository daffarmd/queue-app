<?php

namespace App\Http\Controllers;

use App\Http\Requests\CallQueueRequest;
use App\Http\Requests\CreateQueueRequest;
use App\Models\Queue;
use App\Services\PrinterService;
use App\Services\QueueService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class QueueHandler extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private QueueService $queueService,
        private PrinterService $printerService
    ) {}

    public function store(CreateQueueRequest $request): RedirectResponse
    {
        $queue = $this->queueService->createQueue(
            $request->validated('service_id'),
            $request->validated('patient_name')
        );

        // Attempt to print ticket
        $printed = $this->printerService->printQueueTicket($queue);

        if (! $printed) {
            session()->flash('warning', 'Queue created but printing failed: '.$queue->code);
        } else {
            session()->flash('success', 'Queue created and printed: '.$queue->code);
        }

        return redirect()->back();
    }

    public function call(Queue $queue, CallQueueRequest $request): JsonResponse
    {
        $this->authorize('call', $queue);

        $updatedQueue = $this->queueService->callQueue(
            $queue,
            $request->validated('counter')
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Queue called successfully',
            'queue' => $updatedQueue->load('service'),
        ]);
    }

    public function finish(Queue $queue): JsonResponse
    {
        $this->authorize('manage', $queue);

        $updatedQueue = $this->queueService->finishQueue($queue);

        return response()->json([
            'status' => 'success',
            'message' => 'Queue finished',
            'queue' => $updatedQueue,
        ]);
    }

    public function skip(Queue $queue): JsonResponse
    {
        $this->authorize('manage', $queue);

        $updatedQueue = $this->queueService->skipQueue($queue);

        return response()->json([
            'status' => 'success',
            'message' => 'Queue skipped',
            'queue' => $updatedQueue,
        ]);
    }

    public function recall(Queue $queue, CallQueueRequest $request): JsonResponse
    {
        $this->authorize('recall', $queue);

        $updatedQueue = $this->queueService->recallQueue(
            $queue,
            $request->validated('counter')
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Queue recalled successfully',
            'queue' => $updatedQueue->load('service'),
        ]);
    }
}
