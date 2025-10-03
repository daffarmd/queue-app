// SIMPLE VOICE TEST FOR BROWSER CONSOLE
// Paste this into browser console to test voice announcements

console.log('🔊 SIMPLE VOICE ANNOUNCEMENT TEST');

function testVoiceSimple(message = "Queue Test 001, John Doe, please come to counter 5") {
  console.log('Testing message:', message);

  if ('speechSynthesis' in window) {
    // Cancel any existing speech
    speechSynthesis.cancel();

    // Create utterance with minimal settings
    const utterance = new SpeechSynthesisUtterance(message);
    utterance.rate = 1.0;
    utterance.volume = 1.0;
    utterance.pitch = 1.0;

    // Add simple event handlers
    utterance.onstart = () => console.log('✅ Speech started');
    utterance.onend = () => console.log('✅ Speech finished');
    utterance.onerror = (e) => console.log('❌ Speech error:', e.error);

    // Just speak it without any voice/language settings
    speechSynthesis.speak(utterance);

    console.log('🎤 Speech queued for playback');
  } else {
    console.log('❌ Speech synthesis not supported');
  }
}

// Test with different approaches
function testVoiceCompatibility() {
  console.log('🧪 VOICE COMPATIBILITY TEST');

  // Check basic support
  console.log('Speech synthesis supported:', 'speechSynthesis' in window);

  if ('speechSynthesis' in window) {
    // Check voices
    const voices = speechSynthesis.getVoices();
    console.log('Available voices:', voices.length);

    if (voices.length > 0) {
      console.log('First voice:', voices[0].name, voices[0].lang);
    }

    // Test basic speech
    console.log('Testing basic speech in 2 seconds...');
    setTimeout(() => {
      testVoiceSimple('Testing basic voice synthesis');
    }, 2000);
  }
}

// Make functions globally available
window.testVoiceSimple = testVoiceSimple;
window.testVoiceCompatibility = testVoiceCompatibility;

console.log('✅ Simple voice test functions loaded');
console.log('📞 Run testVoiceSimple() to test basic speech');
console.log('📞 Run testVoiceCompatibility() for full compatibility test');

// Auto-run compatibility test
testVoiceCompatibility();
