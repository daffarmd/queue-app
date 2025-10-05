<?php

namespace App\Http\Controllers;

use App\Services\TTSService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class InstantTTSController extends Controller
{
    public function __construct(private TTSService $ttsService) {}

    /**
     * Instant streaming TTS - no caching, no delays
     */
    public function instantStream(Request $request)
    {
        $request->validate([
            'text' => 'required|string|max:1000',
        ]);

        $text = $this->ttsService->cleanTextForTTS($request->input('text'));

        Log::info('🎤 Instant TTS stream request', ['text' => $text]);

        // Generate audio directly from Piper
        $audioData = $this->ttsService->generateSpeech($text);

        if (! $audioData) {
            return response()->json([
                'error' => 'TTS generation failed',
                'fallback_text' => $text,
            ], 500);
        }

        // Stream immediately with optimal headers
        return response($audioData, 200, [
            'Content-Type' => 'audio/wav',
            'Content-Length' => strlen($audioData),
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'GET, POST',
            'Accept-Ranges' => 'bytes',
        ]);
    }

    /**
     * Instant queue announcement
     */
    public function instantQueue(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'service' => 'required|string',
            'destination' => 'required|string',
            'type' => 'string|in:called,recalled',
            'custom_template' => 'nullable|string|max:500',
        ]);

        $type = $request->input('type', 'called');
        // Priority: explicit custom_template param -> cached dashboard template -> config default
        $template = $request->input('custom_template')
            ?: Cache::get('custom_tts_message')
            ?: config("tts.templates.queue_{$type}");

        // Support both {placeholder} and :placeholder syntaxes
        $replacements = [
            '{queue_code}' => $request->input('code'),
            '{service_name}' => $request->input('service'),
            '{destination_name}' => $request->input('destination'),
            ':code' => $request->input('code'),
            ':service' => $request->input('service'),
            ':destination' => $request->input('destination'),
        ];
        $text = strtr($template, $replacements);

        // Direct generation and streaming
        $cleanText = $this->ttsService->cleanTextForTTS($text);
        $audioData = $this->ttsService->generateSpeech($cleanText);

        if (! $audioData) {
            return response()->json([
                'error' => 'Queue TTS failed',
                'fallback_text' => $cleanText,
            ], 500);
        }

        Log::info('🎤 Instant queue announcement', [
            'code' => $request->input('code'),
            'type' => $type,
            'audio_size' => strlen($audioData),
        ]);

        return response($audioData, 200, [
            'Content-Type' => 'audio/wav',
            'Content-Length' => strlen($audioData),
            'Cache-Control' => 'no-cache',
            'Access-Control-Allow-Origin' => '*',
        ]);
    }

    /**
     * Streaming endpoint that returns audio as data URI
     */
    public function dataUri(Request $request)
    {
        $request->validate([
            'text' => 'required|string|max:500',
        ]);

        $text = $this->ttsService->cleanTextForTTS($request->input('text'));
        $audioData = $this->ttsService->generateSpeech($text);

        if (! $audioData) {
            return response()->json(['error' => 'TTS failed'], 500);
        }

        // Return as base64 data URI for immediate embedding
        $base64Audio = base64_encode($audioData);
        $dataUri = "data:audio/wav;base64,{$base64Audio}";

        return response()->json([
            'success' => true,
            'data_uri' => $dataUri,
            'size' => strlen($audioData),
            'text' => $text,
        ]);
    }

    /**
     * Chunked streaming for very long announcements
     */
    public function streamChunked(Request $request)
    {
        $request->validate([
            'text' => 'required|string|max:2000',
        ]);

        $text = $this->ttsService->cleanTextForTTS($request->input('text'));

        return response()->stream(function () use ($text) {
            // Generate audio
            $audioData = $this->ttsService->generateSpeech($text);

            if ($audioData) {
                // Stream in chunks for better performance
                $chunkSize = 8192; // 8KB chunks
                $offset = 0;
                $length = strlen($audioData);

                while ($offset < $length) {
                    $chunk = substr($audioData, $offset, $chunkSize);
                    echo $chunk;
                    flush();
                    $offset += $chunkSize;
                }
            }
        }, 200, [
            'Content-Type' => 'audio/wav',
            'Cache-Control' => 'no-cache',
            'Access-Control-Allow-Origin' => '*',
        ]);
    }
}
