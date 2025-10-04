<div class="space-y-8">

  <!-- Current Serving Section -->
  <div class="bg-white rounded-xl shadow-lg p-8">
    <h2 class="text-2xl font-bold text-center text-gray-900 mb-6">SEDANG DILAYANI</h2>

    @if($currentQueue)
      <div class="text-center">
        <div class="inline-block bg-[#D32F2F] text-white px-8 py-4 rounded-lg mb-4 relative">
          @if($currentQueue->status === 'recalled')
            <div class="absolute -top-2 -right-2 bg-[#F59E0B] text-white text-xs px-2 py-1 rounded-full font-bold">
              🔄 RECALLED
            </div>
          @endif
          <div class="text-6xl font-bold mb-2">{{ $currentQueue->code }}</div>
          <div class="text-xl">{{ $currentQueue->destination?->name ?? 'Tidak ada tujuan' }}</div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
          <div class="bg-gray-50 p-4 rounded-lg">
            <div class="text-sm text-gray-600">Layanan</div>
            <div class="text-lg font-semibold">{{ $currentQueue->service->name }}</div>
          </div>
          <div class="bg-gray-50 p-4 rounded-lg">
            <div class="text-sm text-gray-600">Tujuan</div>
            <div class="text-lg font-semibold">{{ $currentQueue->destination?->name ?? 'Tidak ada tujuan' }}</div>
          </div>
        </div>
      </div>
    @else
      <div class="text-center py-12">
        <div class="text-gray-400 text-xl">Tidak ada antrian yang sedang dilayani</div>
      </div>
    @endif
  </div>

  <!-- Waiting Queues Section -->
  <div class="bg-white rounded-xl shadow-lg p-6">
    <h3 class="text-xl font-bold text-gray-900 mb-4">Antrian Menunggu</h3>

    @if($waitingQueues && $waitingQueues->count() > 0)
      <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        @foreach($waitingQueues as $queue)
          <div class="bg-gray-50 p-4 rounded-lg text-center border-l-4 border-[#4CAF50]">
            <div class="text-lg font-bold text-gray-900">{{ $queue->code }}</div>
            <div class="text-sm text-gray-600 truncate">{{ $queue->destination?->name ?? 'Tidak ada tujuan' }}</div>
            <div class="text-xs text-gray-500">{{ $queue->service->name }}</div>
          </div>
        @endforeach
      </div>
    @else
      <div class="text-center py-8">
        <div class="text-gray-400">Tidak ada antrian yang menunggu</div>
      </div>
    @endif
  </div>

  <!-- Instructions Section -->
  <div class="bg-gradient-to-r from-[#D32F2F] to-red-600 text-white rounded-xl shadow-lg p-6">
    <div class="text-center">
      <h3 class="text-xl font-bold mb-2">Petunjuk</h3>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
        <div>
          <div class="font-semibold">🎫 Ambil Nomor Antrian</div>
          <div>Dapatkan nomor antrian Anda dari resepsionis</div>
        </div>
        <div>
          <div class="font-semibold">⏰ Menunggu Giliran</div>
          <div>Nomor Anda akan dipanggil ketika siap</div>
        </div>
        <div>
          <div class="font-semibold">🏥 Menuju Tujuan</div>
          <div>Silakan menuju ke tujuan yang ditunjukkan saat dipanggil</div>
        </div>
      </div>
    </div>
  </div>

</div>

<!-- Auto-refresh functionality with voice announcement checking -->
<script>
  // Refresh the component every 5 seconds and check for new calls
  setInterval(() => {
    if (typeof Livewire !== 'undefined') {
      // Use the new checkForNewCalls method that includes voice announcements
      Livewire.dispatch('checkForNewCalls');
    }
  }, 5000);

  // Also keep the regular refresh for fallback
  setInterval(() => {
    if (typeof Livewire !== 'undefined') {
      Livewire.dispatch('refreshData');
    }
  }, 30000);
</script>
