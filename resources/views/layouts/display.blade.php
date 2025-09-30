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

  <!-- Voice Announcement Script -->
  <script>
    document.addEventListener('livewire:initialized', () => {
      Livewire.on('announceQueue', (event) => {
        // Check if speech synthesis is supported
        if ('speechSynthesis' in window) {
          const utterance = new SpeechSynthesisUtterance(event[0].message);
          utterance.lang = 'id-ID'; // Indonesian
          utterance.rate = 0.8;
          utterance.volume = 1.0;
          utterance.pitch = 1.0;

          // Try to use Indonesian voice if available
          const voices = speechSynthesis.getVoices();
          const indonesianVoice = voices.find(voice => voice.lang.startsWith('id'));
          if (indonesianVoice) {
            utterance.voice = indonesianVoice;
          }

          speechSynthesis.speak(utterance);
        }
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
