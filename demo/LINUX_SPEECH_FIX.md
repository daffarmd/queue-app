# Linux Speech Synthesis Fix Guide

## The Problem

You're getting a `synthesis-failed` error because Linux systems often don't have speech synthesis engines properly configured by default.

## Quick Fixes

### 1. Install Required Packages

```bash
# Ubuntu/Debian
sudo apt update
sudo apt install espeak espeak-data speech-dispatcher

# Fedora/RHEL
sudo dnf install espeak espeak-data speech-dispatcher

# Arch Linux
sudo pacman -S espeak espeak-data speech-dispatcher
```

### 2. Start Speech Dispatcher

```bash
# Check if it's running
systemctl --user status speech-dispatcher

# Start it if not running
systemctl --user start speech-dispatcher

# Enable it to start automatically
systemctl --user enable speech-dispatcher
```

### 3. Test Speech from Command Line

```bash
# Test espeak directly
espeak "Hello, this is a test"

# Test speech-dispatcher
spd-say "Hello from speech dispatcher"
```

### 4. Chrome-Specific Fixes

#### Option A: Enable Experimental Features

1. Go to `chrome://flags/#enable-experimental-web-platform-features`
2. Enable the flag
3. Restart Chrome

#### Option B: Launch Chrome with Audio Fix (Development Only)

```bash
google-chrome --no-sandbox --disable-web-security --enable-features=VaapiVideoDecoder
```

### 5. Alternative Browsers

-   **Firefox**: Often works better with Linux speech synthesis
-   **Chromium**: Sometimes has different audio handling

## Testing Steps

1. Run the updated compatibility test
2. Check the console for specific error messages
3. Try different browsers
4. Test command-line speech tools first

## Advanced Troubleshooting

### Check Audio System

```bash
# Check if audio is working
pactl info

# List audio devices
pactl list short sinks
```

### WSL Users

If you're using Windows Subsystem for Linux (WSL), speech synthesis typically doesn't work because WSL doesn't have direct access to Windows audio systems.

### Pulseaudio/Pipewire

Make sure your audio system is properly configured:

```bash
# For PulseAudio
pulseaudio --check -v

# For PipeWire
systemctl --user status pipewire
```

## Expected Results

After applying these fixes, you should see:

-   Voices available in `speechSynthesis.getVoices()`
-   No `synthesis-failed` errors
-   Actual speech output from the browser
