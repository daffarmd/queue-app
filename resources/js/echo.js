import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
  broadcaster: 'pusher',
  key: import.meta.env.VITE_PUSHER_APP_KEY,
  cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'mt1',
  wsHost: import.meta.env.VITE_PUSHER_HOST ? import.meta.env.VITE_PUSHER_HOST : `ws-${import.meta.env.VITE_PUSHER_APP_CLUSTER}.pusherapp.com`,
  wsPort: import.meta.env.VITE_PUSHER_PORT ?? 80,
  wssPort: import.meta.env.VITE_PUSHER_PORT ?? 443,
  forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? 'https') === 'https',
  enabledTransports: ['ws', 'wss'],
});

// Listen for queue events globally
window.Echo.channel('queues')
  .listen('QueueCreated', (e) => {
    console.log('Queue created:', e);
    // Dispatch to Livewire components
    if (typeof Livewire !== 'undefined') {
      Livewire.dispatch('queueCreated', e);
    }
  })
  .listen('QueueCalled', (e) => {
    console.log('Queue called:', e);
    if (typeof Livewire !== 'undefined') {
      Livewire.dispatch('queueCalled', e);
    }
  })
  .listen('QueueRecalled', (e) => {
    console.log('Queue recalled:', e);
    if (typeof Livewire !== 'undefined') {
      Livewire.dispatch('queueRecalled', e);
    }
  });

console.log('Echo initialized for TRI MULYO Queue System');
