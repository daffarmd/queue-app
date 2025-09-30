@props(['queue', 'showActions' => false])

@php
$statusClasses = [
    'waiting' => ['border' => 'border-yellow-400', 'bg' => 'bg-yellow-100', 'text' => 'text-yellow-800'],
    'called' => ['border' => 'border-blue-400', 'bg' => 'bg-blue-100', 'text' => 'text-blue-800'],
    'done' => ['border' => 'border-green-400', 'bg' => 'bg-green-100', 'text' => 'text-green-800'],
    'recalled' => ['border' => 'border-orange-400', 'bg' => 'bg-orange-100', 'text' => 'text-orange-800'],
    'skipped' => ['border' => 'border-red-400', 'bg' => 'bg-red-100', 'text' => 'text-red-800']
];
$classes = $statusClasses[$queue->status] ?? $statusClasses['skipped'];
@endphp

<div class="bg-white rounded-lg shadow-md p-4 border-l-4 {{ $classes['border'] }}">

  <div class="flex justify-between items-start">
    <div class="flex-1">
      <div class="flex items-center space-x-2">
        <h3 class="text-lg font-bold text-gray-900">{{ $queue->code }}</h3>
        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $classes['bg'] }} {{ $classes['text'] }}">
          {{ ucfirst($queue->status) }}
        </span>
      </div>

      <p class="text-gray-700 font-medium">{{ $queue->patient_name }}</p>
      <p class="text-sm text-gray-500">{{ $queue->service->name }}</p>

      @if($queue->counter)
        <p class="text-sm text-gray-600 mt-1">
          <span class="font-medium">Counter:</span> {{ $queue->counter }}
        </p>
      @endif

      @if($queue->called_at)
        <p class="text-xs text-gray-500 mt-1">
          Called: {{ $queue->called_at->format('H:i') }}
        </p>
      @endif
    </div>

    <div class="text-right text-sm text-gray-500">
      #{{ $queue->number }}
    </div>
  </div>

  @if($showActions)
    <div class="mt-4 flex flex-wrap gap-2">
      @if($queue->status === 'waiting')
        <button wire:click="callQueue({{ $queue->id }})"
                class="bg-[#D32F2F] text-white px-3 py-1 rounded text-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-[#D32F2F]">
          Call
        </button>
      @endif

      @if($queue->status === 'called')
        <button wire:click="finishQueue({{ $queue->id }})"
                class="bg-[#4CAF50] text-white px-3 py-1 rounded text-sm hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-[#4CAF50]">
          Done
        </button>
        <button wire:click="skipQueue({{ $queue->id }})"
                class="bg-gray-500 text-white px-3 py-1 rounded text-sm hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500">
          Skip
        </button>
      @endif

      @if($queue->status === 'skipped')
        <button wire:click="recallQueue({{ $queue->id }})"
                class="bg-[#F59E0B] text-white px-3 py-1 rounded text-sm hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-[#F59E0B]">
          Recall
        </button>
      @endif
    </div>
  @endif
</div>
