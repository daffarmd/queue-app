<div class="space-y-6">
  <!-- Page Header -->
    <div class="bg-white rounded-lg shadow p-6">
    <div class="flex justify-between items-center">
      <div>
        <h2 class="text-2xl font-bold text-gray-900">Staff Dashboard</h2>
        <p class="text-gray-600">Manage patient queues and service operations</p>
      </div>
    </div>
  </div>  <!-- Flash Messages -->
  @if (session()->has('success'))
    <x-alert type="success">{{ session('success') }}</x-alert>
  @endif
  @if (session()->has('error'))
    <x-alert type="error">{{ session('error') }}</x-alert>
  @endif
  @if (session()->has('warning'))
    <x-alert type="warning">{{ session('warning') }}</x-alert>
  @endif
  @if (session()->has('info'))
    <x-alert type="info">{{ session('info') }}</x-alert>
  @endif

  <!-- Queue Creation Form -->
  <div class="bg-white rounded-lg shadow p-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Create New Queue</h3>

    <form wire:submit="createQueue" class="space-y-4">
      @csrf

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label for="service" class="block text-sm font-medium text-gray-700 mb-2">
            Service
          </label>
          <select wire:model="selectedService" id="service"
                  class="block w-full rounded-md border-gray-300 shadow-sm focus:border-[#D32F2F] focus:ring-[#D32F2F]">
            <option value="">Select a service...</option>
            @foreach($this->services as $service)
              <option value="{{ $service->id }}">{{ $service->name }} ({{ $service->code }})</option>
            @endforeach
          </select>
          @error('selectedService') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
          <label for="destination" class="block text-sm font-medium text-gray-700 mb-2">
            Destination <span class="text-red-500">*</span>
          </label>
          <select wire:model="selectedDestination" id="destination"
                  class="block w-full rounded-md border-gray-300 shadow-sm focus:border-[#D32F2F] focus:ring-[#D32F2F]">
            <option value="">Select a destination...</option>
            @foreach($this->destinations as $destination)
              <option value="{{ $destination->id }}">{{ $destination->name }} ({{ $destination->code }})</option>
            @endforeach
          </select>
          @error('selectedDestination') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
      </div>

      <div class="flex justify-end">
        <button type="submit"
                class="bg-[#D32F2F] text-white px-6 py-2 rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-[#D32F2F] focus:ring-offset-2">
          Create Queue
        </button>
      </div>
    </form>
  </div>

  <!-- TTS Controls -->
  <div class="bg-white rounded-lg shadow p-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Voice Announcement Controls</h3>

    <div class="space-y-4">
      <div>
        <label for="customTTSMessage" class="block text-sm font-medium text-gray-700 mb-2">
          Custom TTS Message
          <span class="text-xs text-gray-500">(Use {queue_code}, {service_name}, {destination_name} as placeholders)</span>
        </label>
  <textarea wire:model="customTTSMessage" id="customTTSMessage" rows="3"
      class="block w-full rounded-md border-gray-300 shadow-sm focus:border-[#D32F2F] focus:ring-[#D32F2F]"
      data-persist="customTTSMessage"></textarea>
        @error('customTTSMessage') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        <div class="text-xs text-gray-500 mt-1">
          Character count: <span id="tts-char-count">0</span>/500
        </div>
      </div>

      <div class="flex space-x-3">
        <button type="button" onclick="testTTSConnection()"
                class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500">
          🔊 Test TTS
        </button>

        <div id="tts-status" class="flex items-center px-3 py-2 text-sm">
          <span class="loading-spinner hidden animate-spin mr-2">⏳</span>
          <span class="status-text text-gray-600">Ready</span>
        </div>
      </div>
    </div>
  </div>

  <!-- Queue Lists -->
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- Waiting Queues -->
    <div class="bg-white rounded-lg shadow">
      <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">
          Waiting ({{ isset($this->queues['waiting']) ? $this->queues['waiting']->count() : 0 }})
        </h3>
      </div>
      <div class="p-6 space-y-3 max-h-96 overflow-y-auto">
        @if(isset($this->queues['waiting']) && $this->queues['waiting']->count() > 0)
          @foreach($this->queues['waiting'] as $queue)
            <x-queue-card :queue="$queue" :showActions="true" />
          @endforeach
        @else
          <p class="text-gray-500 text-center py-4">No waiting queues</p>
        @endif
      </div>
    </div>

    <!-- Called Queues -->
    <div class="bg-white rounded-lg shadow">
      <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">
          Called ({{ isset($this->queues['called']) ? $this->queues['called']->count() : 0 }})
        </h3>
      </div>
      <div class="p-6 space-y-3 max-h-96 overflow-y-auto">
        @if(isset($this->queues['called']) && $this->queues['called']->count() > 0)
          @foreach($this->queues['called'] as $idx => $queue)
            <x-queue-card :queue="$queue" :showActions="true" :isTopCalled="$idx === 0" />
          @endforeach
        @else
          <p class="text-gray-500 text-center py-4">No called queues</p>
        @endif
      </div>
    </div>

    <!-- Skipped Queues -->
    <div class="bg-white rounded-lg shadow">
      <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">
          Skipped ({{ isset($this->queues['skipped']) ? $this->queues['skipped']->count() : 0 }})
        </h3>
      </div>
      <div class="p-6 space-y-3 max-h-96 overflow-y-auto">
        @if(isset($this->queues['skipped']) && $this->queues['skipped']->count() > 0)
          @foreach($this->queues['skipped'] as $queue)
            <x-queue-card :queue="$queue" :showActions="true" />
          @endforeach
        @else
          <p class="text-gray-500 text-center py-4">No skipped queues</p>
        @endif
      </div>
    </div>

  </div>
</div>

<script>
// TTS announcement function for queue cards (only top called queue has button)
document.addEventListener('DOMContentLoaded', function() {
  let ttsAudio = new Audio();
  ttsAudio.volume = 0.9;

  function playQueueAnnouncement({ code, service, destination, type }) {
    const template = (document.getElementById('customTTSMessage')?.value || '').trim();
    const params = new URLSearchParams({
      code: code,
      service: service,
      destination: destination || '',
      type: type || 'called'
    });
    if (template) {
      params.append('custom_template', template);
    }
    const url = `/tts/instant-queue?${params.toString()}`;

    // Stop previous playback
    try { ttsAudio.pause(); } catch (e) {}
    ttsAudio = new Audio(url);
    ttsAudio.play().catch(err => console.warn('TTS play failed', err));
  }

  document.addEventListener('click', function(e) {
    const targetBtn = e.target.classList.contains('tts-announce-btn') ? e.target : e.target.closest('.tts-announce-btn');
    if (targetBtn) {
      const queueCode = targetBtn.dataset.queueCode;
      const serviceName = targetBtn.dataset.serviceName;
      const destinationName = targetBtn.dataset.destinationName;
      const announceType = targetBtn.dataset.type || 'called';

      playQueueAnnouncement({
        code: queueCode,
        service: serviceName,
        destination: destinationName,
        type: announceType
      });

      // Visual feedback
      const originalText = targetBtn.innerHTML;
      targetBtn.innerHTML = '📢 Announcing...';
      targetBtn.disabled = true;
      setTimeout(() => {
        targetBtn.innerHTML = originalText;
        targetBtn.disabled = false;
      }, 2500);
    }
  });
});

// Global function for announcing queues (can be called from anywhere)
function announceQueue(code, service, destination) {
  const template = (document.getElementById('customTTSMessage')?.value || '').trim();
  const params = new URLSearchParams({ code, service, destination, type: 'called' });
  if (template) {
    params.append('custom_template', template);
  }
  const audio = new Audio(`/tts/instant-queue?${params.toString()}`);
  audio.play().catch(err => console.warn('TTS play failed', err));
}

function testTTSConnection() {
  const statusElement = document.getElementById('tts-status');
  const statusText = statusElement.querySelector('.status-text');
  const spinner = statusElement.querySelector('.loading-spinner');

  spinner.classList.remove('hidden');
  statusText.textContent = 'Testing TTS connection...';
  statusText.className = 'status-text text-blue-600';

  const testMessage = 'TTS system test. This is a test announcement.';
  const testUrl = `/tts/instant?text=${encodeURIComponent(testMessage)}`;
  const audio = new Audio(testUrl);

  let testSuccessful = false;

  audio.oncanplay = () => {
    audio.play()
      .then(() => {
        testSuccessful = true;
        spinner.classList.add('hidden');
        statusText.textContent = 'TTS test successful';
        statusText.className = 'status-text text-green-600';

        setTimeout(() => {
          statusText.textContent = 'Ready';
          statusText.className = 'status-text text-gray-600';
        }, 3000);
      })
      .catch(error => {
        console.warn('TTS test play failed:', error);
        spinner.classList.add('hidden');
        statusText.textContent = 'TTS test failed - Cannot play audio';
        statusText.className = 'status-text text-red-600';

        setTimeout(() => {
          statusText.textContent = 'Ready';
          statusText.className = 'status-text text-gray-600';
        }, 5000);
      });
  };

  audio.onerror = (error) => {
    console.warn('TTS test failed:', error);
    spinner.classList.add('hidden');
    statusText.textContent = 'TTS test failed - Connection error';
    statusText.className = 'status-text text-red-600';

    setTimeout(() => {
      statusText.textContent = 'Ready';
      statusText.className = 'status-text text-gray-600';
    }, 5000);
  };

  setTimeout(() => {
    if (!testSuccessful) {
      spinner.classList.add('hidden');
      statusText.textContent = 'TTS test timeout';
      statusText.className = 'status-text text-red-600';

      setTimeout(() => {
        statusText.textContent = 'Ready';
        statusText.className = 'status-text text-gray-600';
      }, 5000);
    }
  }, 5000);
}

// Character count for TTS message
document.addEventListener('DOMContentLoaded', function() {
  const ttsTextarea = document.getElementById('customTTSMessage');
  const charCount = document.getElementById('tts-char-count');

  if (ttsTextarea && charCount) {
    function updateCharCount() {
      const length = ttsTextarea.value.length;
      charCount.textContent = length;

      // Change color based on length
      if (length > 450) {
        charCount.className = 'text-red-600 font-semibold';
      } else if (length > 400) {
        charCount.className = 'text-orange-600';
      } else {
        charCount.className = 'text-gray-600';
      }
    }

    // Load from localStorage if available
    try {
      const stored = localStorage.getItem('custom_tts_message');
      if (stored && stored !== ttsTextarea.value) {
        ttsTextarea.value = stored;
        // Trigger Livewire sync
        ttsTextarea.dispatchEvent(new Event('input', { bubbles: true }));
      }
    } catch(e) { /* ignore */ }

    ttsTextarea.addEventListener('input', function(e) {
      updateCharCount();
      try { localStorage.setItem('custom_tts_message', ttsTextarea.value); } catch (e) { /* ignore */ }
    });
    ttsTextarea.addEventListener('propertychange', updateCharCount); // IE support

    // Initial count when page loads
    setTimeout(updateCharCount, 100);
  }
});
</script>
