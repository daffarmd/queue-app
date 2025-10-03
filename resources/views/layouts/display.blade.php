<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $title ?? 'TRI MULYO - Queue Display' }}</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-100 font-sans antialiased">
  <div id="audio-unlock-overlay" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 cursor-pointer">
    <div class="text-center text-white">
      <h2 class="text-4xl font-bold mb-4">Click to Enable Sound</h2>
      <p class="text-lg">Please click anywhere on the screen to allow voice announcements.</p>
    </div>
  </div>

  <div class="min-h-screen">
    <!-- Header with TRI MULYO branding -->
    <header class="bg-[#D32F2F] text-white shadow-lg">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-6">
          <div>
            <h1 class="text-3xl font-bold uppercase tracking-wide">TRI MULYO</h1>
            <p class="text-red-200 text-sm">Healthcare Queue Management System</p>
          </div>
          <div class="text-right">
            <div class="text-xl font-semibold" id="currentTime"></div>
            <div class="text-red-200 text-sm">{{ now()->setTimezone('Asia/Jakarta')->format('l, d F Y') }}</div>
          </div>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      @isset($slot)
        {{ $slot }}
      @else
        @yield('content')
      @endif
    </main>
  </div>

  <!-- Real-time clock script -->
  <script>
    function updateClock() {
      const now = new Date();
      const timeString = now.toLocaleTimeString('id-ID', {
        timeZone: 'Asia/Jakarta',
        hour12: false
      });
      document.getElementById('currentTime').textContent = timeString + ' WIB';
    }

    updateClock();
    setInterval(updateClock, 1000);
  </script>

  <!-- TTS Voice Announcement Script -->
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const audioUnlockOverlay = document.getElementById('audio-unlock-overlay');
      const audio = new Audio();
      audio.volume = 0.9;
      audio.preload = 'auto';

      function unlockAudio() {
        audioUnlockOverlay.style.display = 'none';
        // Play a silent sound to unlock autoplay
        audio.src = "data:audio/wav;base64,UklGRigAAABXQVZFZm10IBIAAAABAAEARKwAAIhYAQACABAAAABkYXRhAgAAAAEA";
        audio.play().catch(e => console.warn("Could not play silent audio:", e));
        document.removeEventListener('click', unlockAudio);
        document.removeEventListener('keydown', unlockAudio);
      }

      document.addEventListener('click', unlockAudio);
      document.addEventListener('keydown', unlockAudio);

      async function playTTS(url, fallbackMessage) {
        if (audio.src && !audio.paused) {
          audio.pause();
        }

        return new Promise((resolve, reject) => {
          audio.src = url;

          const playPromise = audio.play();

          if (playPromise !== undefined) {
            playPromise.then(() => {
              // Autoplay started!
              const endListener = () => {
                audio.removeEventListener('ended', endListener);
                resolve();
              };
              audio.addEventListener('ended', endListener);
            }).catch(error => {
              console.error('TTS play failed:', error);
              reject(error);
            });
          } else {
            // play() doesn't return a promise in some browsers
             const endListener = () => {
                audio.removeEventListener('ended', endListener);
                resolve();
              };
              audio.addEventListener('ended', endListener);
          }

          const errorListener = (err) => {
            audio.removeEventListener('error', errorListener);
            console.error('TTS audio element error:', err);
            reject(err);
          };
          audio.addEventListener('error', errorListener);
        });
      }

      Livewire.on('announceTTS', async (event) => {
        const data = event[0];
        let customTemplate = '';
        const textarea = document.getElementById('customTTSMessage');
        if (textarea && textarea.value.trim().length > 0) {
          customTemplate = textarea.value.trim();
        }
        const params = new URLSearchParams({
          code: data.code || '',
          service: data.service || '',
          destination: data.destination || '',
          type: data.type || 'called'
        });
        if (customTemplate) {
          params.append('custom_template', customTemplate);
        }
        const instantUrl = `/tts/instant-queue?${params.toString()}`;
        try {
          await playTTS(instantUrl);
        } catch (error) {
          console.error('TTS failed:', error.message);
        }
      });

      Livewire.on('sendCustomTTS', async (event) => {
        const data = event[0];
        const instantUrl = `/tts/instant?text=${encodeURIComponent(data.message)}`;
        try {
          await playTTS(instantUrl);
        } catch (error) {
          console.warn('Custom TTS failed:', error.message);
        }
      });

      document.addEventListener('click', function(e) {
        const button = e.target.closest('.tts-announce-btn');
        if (button) {
          const queueCode = button.dataset.queueCode;
          const serviceName = button.dataset.serviceName;
          const destinationName = button.dataset.destinationName;

          if (queueCode && serviceName) {
            const message = `Queue ${queueCode} to ${destinationName || serviceName}`;
            const instantUrl = `/tts/instant?text=${encodeURIComponent(message)}`;
            playTTS(instantUrl).catch(error => console.warn('TTS play failed:', error));
          }
        }
      });
    });

    // Auto-refresh display every 30 seconds
    setInterval(() => {
      if (typeof Livewire !== 'undefined') {
        Livewire.dispatch('refreshData');
      }
    }, 30000);

    // TTS Controls for Staff Dashboard
    function updateCharCount() {
      const textarea = document.getElementById('customTTSMessage');
      const counter = document.getElementById('tts-char-count');
      if (textarea && counter) {
        counter.textContent = textarea.value.length;
      }
    }

    async function testTTSConnection() {
      const statusEl = document.getElementById('tts-status');
      const statusText = statusEl.querySelector('.status-text');
      const spinner = statusEl.querySelector('.loading-spinner');

      spinner.classList.remove('hidden');
      statusText.textContent = 'Testing...';
      statusText.className = 'status-text text-blue-600';

      try {
        const healthResponse = await fetch('/tts/health');
        const healthData = await healthResponse.json();

        if (healthData.piper_available) {
          const testResponse = await fetch('/tts/test');

          if (testResponse.ok) {
            const audioBlob = await testResponse.blob();
            const audioUrl = URL.createObjectURL(audioBlob);
            const audio = new Audio(audioUrl);

            audio.onended = () => {
              URL.revokeObjectURL(audioUrl);
              statusText.textContent = 'TTS Connected';
              statusText.className = 'status-text text-green-600';
            };

            await audio.play();
          } else {
            throw new Error('TTS generation failed');
          }
        } else {
          throw new Error('Piper TTS server not available');
        }

      } catch (error) {
        console.warn('TTS test failed:', error);
        statusText.textContent = 'TTS Offline';
        statusText.className = 'status-text text-red-600';
      } finally {
        spinner.classList.add('hidden');

        setTimeout(() => {
          statusText.textContent = 'Ready';
          statusText.className = 'status-text text-gray-600';
        }, 3000);
      }
    }

    document.addEventListener('DOMContentLoaded', () => {
      const textarea = document.getElementById('customTTSMessage');
      if (textarea) {
        textarea.addEventListener('input', updateCharCount);
        updateCharCount();
      }
    });

    document.addEventListener('livewire:initialized', () => {
      Livewire.hook('morph.updated', () => {
        updateCharCount();
      });
    });
  </script>
</body>
</html>
