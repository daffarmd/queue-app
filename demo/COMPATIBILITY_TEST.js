// COMPATIBILITY_TEST.js - Minimal browser compatibility test for speech synthesis
console.log('🔍 COMPATIBILITY TEST - Speech Synthesis API Diagnostic');

// Basic API availability check
function checkAPISupport() {
    console.log('\n=== API SUPPORT CHECK ===');

    if (!('speechSynthesis' in window)) {
        console.error('❌ SpeechSynthesis API not supported');
        return false;
    }

    if (!('SpeechSynthesisUtterance' in window)) {
        console.error('❌ SpeechSynthesisUtterance not supported');
        return false;
    }

    console.log('✅ SpeechSynthesis API available');
    console.log('✅ SpeechSynthesisUtterance available');

    // Check API properties
    console.log('📊 speechSynthesis.speaking:', speechSynthesis.speaking);
    console.log('📊 speechSynthesis.pending:', speechSynthesis.pending);
    console.log('📊 speechSynthesis.paused:', speechSynthesis.paused);

    return true;
}

// Simple voice test without any configuration
function simpleVoiceTest() {
    console.log('\n=== SIMPLE VOICE TEST ===');

    if (!checkAPISupport()) {
        return;
    }

    // Cancel any existing speech
    speechSynthesis.cancel();

    const message = "Hello. This is a basic speech test.";
    const utterance = new SpeechSynthesisUtterance(message);

    // No configuration at all - use browser defaults
    console.log('🎤 Speaking with default settings:', message);

    let testComplete = false;

    utterance.onstart = () => {
        console.log('✅ Speech started successfully');
        testComplete = true;
    };

    utterance.onend = () => {
        console.log('✅ Speech completed successfully');
    };

    utterance.onerror = (event) => {
        console.error('❌ Speech error:', event.error);
        console.error('❌ Error details:', event);
        testComplete = true;
    };

    speechSynthesis.speak(utterance);

    // Check if it started within 2 seconds
    setTimeout(() => {
        if (!testComplete) {
            console.log('⚠️ Speech did not start within 2 seconds');
            console.log('📊 Current state - speaking:', speechSynthesis.speaking, 'pending:', speechSynthesis.pending);
        }
    }, 2000);
}

// Platform-specific information
function platformInfo() {
    console.log('\n=== PLATFORM INFORMATION ===');
    console.log('🖥️ User Agent:', navigator.userAgent);
    console.log('🖥️ Platform:', navigator.platform);
    console.log('🌐 Language:', navigator.language);
    console.log('🌐 Languages:', navigator.languages);
    console.log('🎨 Color Scheme:', window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');

    // Check if running in secure context
    console.log('🔒 Secure Context:', window.isSecureContext);

    // Check protocol
    console.log('🌐 Protocol:', location.protocol);
    console.log('🌐 Host:', location.host);
}

// Voice enumeration test
function voiceEnumerationTest() {
    console.log('\n=== VOICE ENUMERATION TEST ===');

    const voices = speechSynthesis.getVoices();
    console.log('🎭 Available voices count:', voices.length);

    if (voices.length === 0) {
        console.log('⚠️ No voices found immediately, waiting for voiceschanged event...');

        speechSynthesis.addEventListener('voiceschanged', () => {
            const newVoices = speechSynthesis.getVoices();
            console.log('🎭 Available voices after event:', newVoices.length);

            newVoices.slice(0, 3).forEach((voice, index) => {
                console.log(`🎤 Voice ${index + 1}: ${voice.name} (${voice.lang}) [${voice.default ? 'DEFAULT' : 'standard'}]`);
            });
        }, { once: true });
    } else {
        voices.slice(0, 5).forEach((voice, index) => {
            console.log(`🎤 Voice ${index + 1}: ${voice.name} (${voice.lang}) [${voice.default ? 'DEFAULT' : 'standard'}]`);
        });
    }
}

// Permissions check (if applicable)
function checkPermissions() {
    console.log('\n=== PERMISSIONS CHECK ===');

    if ('permissions' in navigator) {
        // Some browsers might have speech-related permissions
        console.log('✅ Permissions API available');
    } else {
        console.log('⚠️ Permissions API not available');
    }

    // Check for potential blocking factors
    if (document.hidden) {
        console.log('⚠️ Document is hidden');
    }

    if (!document.hasFocus()) {
        console.log('⚠️ Document does not have focus');
    }
}

// Run all tests
function runCompatibilityTest() {
    console.log('🚀 Starting comprehensive compatibility test...\n');

    platformInfo();
    checkPermissions();
    voiceEnumerationTest();

    // Wait a moment for voices to load, then test
    setTimeout(() => {
        simpleVoiceTest();
    }, 1000);
}

// Auto-run when loaded
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', runCompatibilityTest);
} else {
    runCompatibilityTest();
}

// Export for manual testing
window.compatibilityTest = {
    run: runCompatibilityTest,
    simple: simpleVoiceTest,
    platform: platformInfo,
    voices: voiceEnumerationTest,
    api: checkAPISupport
};

console.log('🎯 Compatibility test loaded. Run compatibilityTest.run() to test again.');
