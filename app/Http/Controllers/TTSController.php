<?php

namespace App\Http\Controllers;

use App\Services\TTSService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class TTSController extends Controller
{
    public function __construct(private TTSService $ttsService) {}

    /**
     * Generate and stream TTS audio
     */
    public function generateAudio(Request $request)
    {
        $request->validate([
            'text' => 'required|string|max:500',
            'cache' => 'boolean',
        ]);

        $text = $request->input('text');
        $useCache = $request->boolean('cache', false);

        // Clean text for better pronunciation
        $cleanText = $this->ttsService->cleanTextForTTS($text);

        // Check cache if enabled
        $cacheKey = 'tts_audio_'.md5($cleanText);

        if ($useCache && Cache::has($cacheKey)) {
            $audioData = Cache::get($cacheKey);
            Log::info('TTS audio served from cache', ['text_hash' => md5($cleanText)]);
        } else {
            // Generate new audio
            $audioData = $this->ttsService->generateSpeech($cleanText);

            if (! $audioData) {
                return response()->json([
                    'error' => 'Failed to generate speech audio',
                    'fallback_text' => $cleanText,
                ], 500);
            }

            // Cache if enabled
            if ($useCache && config('tts.cache_audio')) {
                Cache::put($cacheKey, $audioData, config('tts.cache_duration'));
            }
        }

        return response($audioData, 200)
            ->header('Content-Type', 'audio/wav')
            ->header('Content-Disposition', 'inline; filename="speech.wav"')
            ->header('Cache-Control', 'public, max-age=3600')
            ->header('Access-Control-Allow-Origin', '*');
    }

    /**
     * Generate queue announcement audio
     */
    public function queueAnnouncement(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'service' => 'required|string',
            'destination' => 'required|string',
            'type' => 'string|in:called,recalled',
        ]);

        $type = $request->input('type', 'called');
        $template = config("tts.templates.queue_{$type}");

        $text = str_replace(
            [':code', ':service', ':destination'],
            [
                $request->input('code'),
                $request->input('service'),
                $request->input('destination'),
            ],
            $template
        );

        return $this->generateAudio(new Request(['text' => $text, 'cache' => true]));
    }

    /**
     * Check TTS service health
     */
    public function health()
    {
        $config = $this->ttsService->getConfig();

        return response()->json([
            'status' => 'ok',
            'tts_enabled' => $config['enabled'],
            'piper_available' => $config['available'],
            'piper_url' => $config['piper_url'],
            'fallback_browser' => config('tts.fallback_to_browser'),
            'templates' => config('tts.templates'),
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Test TTS with sample text
     */
    public function test(Request $request)
    {
        $sampleTexts = [
            'Selamat datang di Klinik TRI MULYO',
            'Nomor antrian GEN-001, layanan General, silakan menuju ke Reception',
            'Terima kasih telah menggunakan layanan kami',
        ];

        $text = $request->input('text', $sampleTexts[array_rand($sampleTexts)]);

        return $this->generateAudio(new Request(['text' => $text]));
    }

    /**
     * Generate custom announcement
     */
    public function customAnnouncement(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'queue_code' => 'string',
            'service_name' => 'string',
            'destination_name' => 'string',
        ]);

        $message = $request->input('message');

        // Replace placeholders if provided
        if ($request->has('queue_code')) {
            $message = str_replace(
                ['{queue_code}', '{service_name}', '{destination_name}'],
                [
                    $request->input('queue_code', ''),
                    $request->input('service_name', ''),
                    $request->input('destination_name', ''),
                ],
                $message
            );
        }

        return $this->generateAudio(new Request(['text' => $message, 'cache' => false]));
    }
}
