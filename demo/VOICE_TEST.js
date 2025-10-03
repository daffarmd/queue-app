// ROBUST VOICE ANNOUNCEMENT BROWSER TEST
// Copy and paste this into browser console on the display page

console.log('🔊 TESTING ROBUST VOICE ANNOUNCEMENTS IN BROWSER');

// System Information
console.log('🖥️ SYSTEM INFO:');
console.log('Browser:', navigator.userAgent);
console.log('Platform:', navigator.platform);
console.log('Language:', navigator.language);

// Test 1: Check if SpeechSynthesis is available
if ('speechSynthesis' in window) {
  console.log('✅ SpeechSynthesis API is supported');

  // Wait for voices to load
  function waitForVoices() {
    return new Promise((resolve) => {
      if (speechSynthesis.getVoices().length > 0) {
        resolve();
      } else {
        speechSynthesis.addEventListener('voiceschanged', resolve, { once: true });
        // Fallback timeout
        setTimeout(resolve, 2000);
      }
    });
  }

  // Test 2: Get available voices
  waitForVoices().then(() => {
    const voices = speechSynthesis.getVoices();
    console.log('📢 Available voices:', voices.length);

    if (voices.length === 0) {
      console.log('⚠️ No voices available - this might be the problem');
    } else {
      voices.forEach((voice, index) => {
        console.log(`${index + 1}. ${voice.name} (${voice.lang}) ${voice.default ? '[DEFAULT]' : ''}`);
      });
    }

    // Find Indonesian voice
    const indonesianVoice = voices.find(voice => voice.lang.startsWith('id'));
    if (indonesianVoice) {
      console.log('✅ Indonesian voice found:', indonesianVoice.name);
    } else {
      console.log('⚠️  No Indonesian voice found, will use alternatives');
    }
  });

  // Test 3: Robust voice announcement with multiple strategies
  async function testVoiceAnnouncement(message = "Queue GEN-002, Test Patient Voice, please come to counter 7") {
    console.log('🎤 Testing robust voice with message:', message);

    // Strategy 1: Ultra-minimal approach
    async function tryMinimalSpeech() {
      return new Promise((resolve) => {
        console.log('🎯 Strategy 1: Ultra-minimal speech');
        speechSynthesis.cancel(); // Clear any existing speech

        const utterance = new SpeechSynthesisUtterance(message);
        // Absolutely minimal settings

        let resolved = false;
        utterance.onstart = () => {
          if (!resolved) {
            console.log('✅ Minimal speech started');
            resolved = true;
            resolve(true);
          }
        };
        utterance.onend = () => console.log('✅ Minimal speech ended');
        utterance.onerror = (e) => {
          if (!resolved) {
            console.log('❌ Minimal speech failed:', e.error);
            resolved = true;
            resolve(false);
          }
        };

        speechSynthesis.speak(utterance);

        // Timeout
        setTimeout(() => {
          if (!resolved) {
            console.log('⏰ Minimal speech timeout');
            resolved = true;
            resolve(false);
          }
        }, 3000);
      });
    }

    // Strategy 2: Try with browser's default voice
    async function tryDefaultVoice() {
      return new Promise((resolve) => {
        console.log('🎯 Strategy 2: Browser default voice');
        const voices = speechSynthesis.getVoices();

        if (voices.length === 0) {
          resolve(false);
          return;
        }

        const utterance = new SpeechSynthesisUtterance(message);
        const defaultVoice = voices.find(v => v.default) || voices[0];
        utterance.voice = defaultVoice;
        utterance.rate = 1.0;

        console.log('Using default voice:', defaultVoice.name);

        let resolved = false;
        utterance.onstart = () => {
          if (!resolved) {
            console.log('✅ Default voice started');
            resolved = true;
            resolve(true);
          }
        };
        utterance.onend = () => console.log('✅ Default voice ended');
        utterance.onerror = (e) => {
          if (!resolved) {
            console.log('❌ Default voice failed:', e.error);
            resolved = true;
            resolve(false);
          }
        };

        speechSynthesis.speak(utterance);

        setTimeout(() => {
          if (!resolved) {
            console.log('⏰ Default voice timeout');
            resolved = true;
            resolve(false);
          }
        }, 3000);
      });
    }

    // Strategy 3: Text notification
    function showTextNotification() {
      console.log('🎯 Strategy 3: Text notification fallback');

      // Remove any existing notifications
      const existing = document.querySelector('.voice-test-notification');
      if (existing) existing.remove();

      const notification = document.createElement('div');
      notification.className = 'voice-test-notification';
      notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: #D32F2F;
                color: white;
                padding: 15px 20px;
                border-radius: 8px;
                font-size: 16px;
                font-weight: bold;
                z-index: 10000;
                box-shadow: 0 4px 12px rgba(0,0,0,0.3);
                max-width: 400px;
                animation: slideIn 0.3s ease-out;
            `;

      // Add animation CSS if not exists
      if (!document.querySelector('#voice-test-styles')) {
        const style = document.createElement('style');
        style.id = 'voice-test-styles';
        style.textContent = `
                    @keyframes slideIn {
                        from { transform: translateX(100%); opacity: 0; }
                        to { transform: translateX(0); opacity: 1; }
                    }
                `;
        document.head.appendChild(style);
      }

      notification.innerHTML = `
                <div style="font-size: 14px; margin-bottom: 5px; opacity: 0.9;">🔊 VOICE TEST NOTIFICATION</div>
                <div>${message}</div>
                <div style="font-size: 12px; margin-top: 8px; opacity: 0.7;">Speech synthesis failed - showing text instead</div>
            `;

      document.body.appendChild(notification);

      // Remove after 8 seconds
      setTimeout(() => {
        if (notification.parentNode) {
          notification.style.animation = 'slideIn 0.3s ease-out reverse';
          setTimeout(() => {
            if (notification.parentNode) {
              notification.parentNode.removeChild(notification);
            }
          }, 300);
        }
      }, 8000);

      console.log('✅ Text notification displayed');
    }

    // Wait for voices first
    await waitForVoices();

    // Try strategies in order
    const minimal = await tryMinimalSpeech();
    if (minimal) {
      console.log('🎉 Success with minimal speech!');
      return;
    }

    const defaultVoice = await tryDefaultVoice();
    if (defaultVoice) {
      console.log('🎉 Success with default voice!');
      return;
    }

    // All speech failed, show notification
    console.log('⚠️ All speech attempts failed, showing text notification');
    showTextNotification();

    return '📱 Text notification shown (speech synthesis not working)';
  }

  // Test 4: Simulate Livewire event
  function simulateLivewireVoiceEvent() {
    if (typeof Livewire !== 'undefined') {
      console.log('🔧 Simulating Livewire announceQueue event');

      // This simulates what happens when the component dispatches the event
      const event = [{
        message: "Queue GEN-002, Test Patient Voice, please come to counter 7"
      }];

      // Trigger the same logic as in the display layout
      console.log('Voice announcement triggered:', event);

      const message = event[0].message;
      console.log('Speaking message:', message);

      const utterance = new SpeechSynthesisUtterance(message);
      utterance.lang = 'id-ID';
      utterance.rate = 0.8;
      utterance.volume = 1.0;
      utterance.pitch = 1.0;

      const voices = speechSynthesis.getVoices();
      const indonesianVoice = voices.find(voice => voice.lang.startsWith('id'));
      if (indonesianVoice) {
        utterance.voice = indonesianVoice;
        console.log('Using Indonesian voice:', indonesianVoice.name);
      } else {
        console.log('No Indonesian voice found, using default voice');
      }

      utterance.onstart = () => console.log('Speech started');
      utterance.onend = () => console.log('Speech ended');
      utterance.onerror = (e) => console.error('Speech error:', e);

      speechSynthesis.speak(utterance);
      return '✅ Livewire voice simulation completed';
    } else {
      return '❌ Livewire not found';
    }
  }

  // Make functions available globally
  window.testVoiceAnnouncement = testVoiceAnnouncement;
  window.simulateLivewireVoiceEvent = simulateLivewireVoiceEvent;

  console.log('🎯 AVAILABLE TEST FUNCTIONS:');
  console.log('📞 testVoiceAnnouncement() - Test basic voice synthesis');
  console.log('📞 testVoiceAnnouncement("Your custom message") - Test with custom message');
  console.log('📞 simulateLivewireVoiceEvent() - Simulate complete Livewire event');
  console.log('');
  console.log('🚀 AUTO-RUNNING BASIC TEST IN 2 SECONDS...');

  // Auto-run test after 2 seconds
  setTimeout(() => {
    testVoiceAnnouncement();
  }, 2000);

} else {
  console.error('❌ SpeechSynthesis API not supported in this browser');
}

// Instructions
console.log('');
console.log('📋 MANUAL TEST INSTRUCTIONS:');
console.log('1. Ensure your browser volume is on');
console.log('2. Allow audio if browser asks for permission');
console.log('3. Run testVoiceAnnouncement() to test voice');
console.log('4. Open staff page in another tab and call a queue');
console.log('5. Watch this console for "Voice announcement triggered" messages');
