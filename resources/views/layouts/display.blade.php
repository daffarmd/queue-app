<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $title ?? 'TRI MULYO - Queue Display' }}</title>
  
  <!-- Favicon -->
  <link rel="icon" type="image/x-icon" href="{{ asset('icon-tri-mulyo.png') }}">
  
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-100 font-sans antialiased">
  <div class="min-h-screen">
    <!-- Header with TRI MULYO branding -->
    <header class="bg-[#D32F2F] text-white shadow-lg">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
          <div class="flex items-center space-x-6">
            <img src="{{ asset('icon-tri-mulyo.png') }}" alt="TRI MULYO Logo" class="h-20 w-20 object-contain">
            <div>
              <h1 class="text-4xl font-bold uppercase tracking-wide">TRI MULYO</h1>
              <p class="text-red-200 text-base">Healthcare Queue Management System</p>
            </div>
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

  <!-- Voice Announcement Script -->
  <script>
    document.addEventListener('livewire:initialized', () => {
      console.log('Livewire initialized, setting up voice announcements...');

      // Track current speech state
      let currentUtterance = null;
      let isAnnouncing = false;

      // Wait for voices to be loaded
      function waitForVoices() {
        return new Promise((resolve) => {
          if (speechSynthesis.getVoices().length > 0) {
            resolve();
          } else {
            speechSynthesis.addEventListener('voiceschanged', resolve, { once: true });
          }
        });
      }

      // Stop any ongoing speech
      function stopCurrentSpeech() {
        if (isAnnouncing && speechSynthesis.speaking) {
          console.log('🛑 Stopping current speech announcement');
          speechSynthesis.cancel();
          currentUtterance = null;
          isAnnouncing = false;
        }
      }

      // Voice announcement function with multiple fallback strategies
      async function announceMessage(message) {
        console.log('🔊 Attempting to announce:', message);

        // Stop any ongoing speech before starting new one
        stopCurrentSpeech();

        // Strategy 1: Try with system default (no specific voice)
        async function tryBasicSpeech() {
          return new Promise((resolve) => {
            console.log('🎯 Strategy 1: Basic speech synthesis');
            const utterance = new SpeechSynthesisUtterance(message);
            // Use minimal settings for maximum compatibility
            utterance.rate = 1.0;
            utterance.volume = 1.0;
            utterance.pitch = 1.0;
            // Don't set language or voice - let browser decide

            currentUtterance = utterance;
            isAnnouncing = true;

            utterance.onstart = () => {
              console.log('✅ Basic speech started');
              resolve(true);
            };
            utterance.onend = () => {
              console.log('✅ Basic speech ended');
              currentUtterance = null;
              isAnnouncing = false;
            };
            utterance.onerror = (e) => {
              console.log('❌ Basic speech failed:', e.error);
              currentUtterance = null;
              isAnnouncing = false;
              resolve(false);
            };

            speechSynthesis.speak(utterance);

            // Timeout after 3 seconds if nothing happens
            setTimeout(() => {
              if (currentUtterance === utterance && isAnnouncing) {
                currentUtterance = null;
                isAnnouncing = false;
                resolve(false);
              }
            }, 3000);
          });
        }

        // Strategy 2: Try with available voices
        async function tryWithVoices() {
          return new Promise((resolve) => {
            console.log('🎯 Strategy 2: Using available voices');
            const voices = speechSynthesis.getVoices();
            console.log('Available voices:', voices.length);

            if (voices.length === 0) {
              resolve(false);
              return;
            }

            const utterance = new SpeechSynthesisUtterance(message);
            // Use the first available voice
            utterance.voice = voices[0];
            utterance.lang = voices[0].lang;
            utterance.rate = 1.0;
            utterance.volume = 1.0;
            utterance.pitch = 1.0;

            console.log('Using voice:', voices[0].name, voices[0].lang);

            currentUtterance = utterance;
            isAnnouncing = true;

            utterance.onstart = () => {
              console.log('✅ Voice-specific speech started');
              resolve(true);
            };
            utterance.onend = () => {
              console.log('✅ Voice-specific speech ended');
              currentUtterance = null;
              isAnnouncing = false;
            };
            utterance.onerror = (e) => {
              console.log('❌ Voice-specific speech failed:', e.error);
              currentUtterance = null;
              isAnnouncing = false;
              resolve(false);
            };

            speechSynthesis.speak(utterance);
            setTimeout(() => {
              if (currentUtterance === utterance && isAnnouncing) {
                currentUtterance = null;
                isAnnouncing = false;
                resolve(false);
              }
            }, 3000);
          });
        }

        // Strategy 3: Silent fallback (no visual notification)
        function silentFallback() {
          console.log('🎯 Strategy 3: Silent fallback - no visual notification');
          console.log('⚠️ Speech synthesis failed or not supported, continuing silently');
        }

        // Execute strategies in order
        if ('speechSynthesis' in window) {
          // Wait for voices to load
          await waitForVoices();

          // Try basic speech first
          const basicSuccess = await tryBasicSpeech();
          if (basicSuccess) return;

          // Try with specific voices
          const voiceSuccess = await tryWithVoices();
          if (voiceSuccess) return;

          // If all speech attempts fail, use silent fallback
          console.log('⚠️ All speech synthesis attempts failed, using silent fallback');
          silentFallback();
        } else {
          console.log('⚠️ Speech synthesis not supported, using silent fallback');
          silentFallback();
        }
      }

      Livewire.on('announceQueue', async (event) => {
        console.log('Voice announcement triggered:', event);
        const message = event[0].message;
        await announceMessage(message);
      });
    });

    // Auto-refresh display every 30 seconds
    setInterval(() => {
      if (typeof Livewire !== 'undefined') {
        Livewire.dispatch('refreshData');
      }
    }, 30000);
  </script>
</body>
</html>
