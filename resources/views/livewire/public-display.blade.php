<div class="space-y-8">

  <!-- Current Serving Section -->
  <div class="bg-white rounded-xl shadow-lg p-8">
    <h2 class="text-2xl font-bold text-center text-gray-900 mb-6">NOW SERVING</h2>

    @if($currentQueue)
      <div class="text-center">
        <div class="inline-block bg-[#D32F2F] text-white px-8 py-4 rounded-lg mb-4">
          <div class="text-6xl font-bold mb-2">{{ $currentQueue->code }}</div>
          <div class="text-xl">{{ $currentQueue->patient_name }}</div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
          <div class="bg-gray-50 p-4 rounded-lg">
            <div class="text-sm text-gray-600">Service</div>
            <div class="text-lg font-semibold">{{ $currentQueue->service->name }}</div>
          </div>
          <div class="bg-gray-50 p-4 rounded-lg">
            <div class="text-sm text-gray-600">Counter</div>
            <div class="text-lg font-semibold">{{ $currentQueue->counter ?? 'N/A' }}</div>
          </div>
        </div>
      </div>
    @else
      <div class="text-center py-12">
        <div class="text-gray-400 text-xl">No queue currently being served</div>
      </div>
    @endif
  </div>

  <!-- Recently Called Section -->
  @if($calledQueues && $calledQueues->count() > 1)
    <div class="bg-white rounded-xl shadow-lg p-6">
      <h3 class="text-xl font-bold text-gray-900 mb-4">Recently Called</h3>
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @foreach($calledQueues->skip(1)->take(4) as $queue)
          <div class="bg-blue-50 p-4 rounded-lg text-center">
            <div class="text-lg font-bold text-blue-700">{{ $queue->code }}</div>
            <div class="text-sm text-gray-600">{{ $queue->service->name }}</div>
            <div class="text-sm text-blue-600">Counter {{ $queue->counter ?? 'N/A' }}</div>
          </div>
        @endforeach
      </div>
    </div>
  @endif

  <!-- Recalled Queues Section -->
  @if($recalledQueues && $recalledQueues->count() > 0)
    <div class="bg-white rounded-xl shadow-lg p-6">
      <h3 class="text-xl font-bold text-gray-900 mb-4">🔄 Recalled Queues</h3>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @foreach($recalledQueues as $queue)
          <div class="bg-[#F59E0B] text-white p-4 rounded-lg text-center">
            <div class="text-lg font-bold">{{ $queue->code }}</div>
            <div class="text-sm">{{ $queue->patient_name }}</div>
            <div class="text-xs opacity-90">{{ $queue->service->name }}</div>
            <div class="text-sm font-semibold mt-1">Counter {{ $queue->counter ?? 'N/A' }}</div>
          </div>
        @endforeach
      </div>
    </div>
  @endif

  <!-- Waiting Queues Section -->
  <div class="bg-white rounded-xl shadow-lg p-6">
    <h3 class="text-xl font-bold text-gray-900 mb-4">Waiting Queue</h3>

    @if($waitingQueues && $waitingQueues->count() > 0)
      <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        @foreach($waitingQueues as $queue)
          <div class="bg-gray-50 p-4 rounded-lg text-center border-l-4 border-[#4CAF50]">
            <div class="text-lg font-bold text-gray-900">{{ $queue->code }}</div>
            <div class="text-sm text-gray-600 truncate">{{ $queue->patient_name }}</div>
            <div class="text-xs text-gray-500">{{ $queue->service->name }}</div>
          </div>
        @endforeach
      </div>
    @else
      <div class="text-center py-8">
        <div class="text-gray-400">No queues waiting</div>
      </div>
    @endif
  </div>

  <!-- Instructions Section -->
  <div class="bg-gradient-to-r from-[#D32F2F] to-red-600 text-white rounded-xl shadow-lg p-6">
    <div class="text-center">
      <h3 class="text-xl font-bold mb-2">Instructions</h3>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
        <div>
          <div class="font-semibold">🎫 Take Your Number</div>
          <div>Get your queue number from the reception</div>
        </div>
        <div>
          <div class="font-semibold">⏰ Wait for Your Turn</div>
          <div>Your number will be called when ready</div>
        </div>
        <div>
          <div class="font-semibold">🏥 Go to Counter</div>
          <div>Proceed to the indicated counter when called</div>
        </div>
      </div>
    </div>
  </div>

</div>

<!-- Auto-refresh functionality -->
<script>
  // Refresh the component every 10 seconds
  setInterval(() => {
    if (typeof Livewire !== 'undefined') {
      Livewire.dispatch('refreshData');
    }
  }, 10000);
</script>
