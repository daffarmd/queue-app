<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TTSService
{
    private string $piperUrl;

    private int $timeout;

    private bool $enabled;

    public function __construct()
    {
        $this->piperUrl = config('tts.piper_url', 'http://127.0.0.1:5000');
        $this->timeout = config('tts.timeout', 10);
        $this->enabled = config('tts.enabled', true);
    }

    /**
     * Generate speech audio from text using Piper TTS
     */
    public function generateSpeech(string $text): ?string
    {
        if (! $this->enabled) {
            Log::info('TTS is disabled, skipping speech generation');

            return null;
        }

        if (empty(trim($text))) {
            Log::warning('Empty text provided for TTS generation');

            return null;
        }

        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'audio/wav',
                ])
                ->post($this->piperUrl, [
                    'text' => $text,
                ]);

            if ($response->successful()) {
                Log::info('TTS speech generated successfully', ['text_length' => strlen($text)]);

                return $response->body();
            }

            Log::error('TTS generation failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;

        } catch (\Exception $e) {
            Log::error('TTS service error: '.$e->getMessage(), [
                'text' => $text,
                'piper_url' => $this->piperUrl,
            ]);

            return null;
        }
    }

    /**
     * Check if Piper TTS service is available
     */
    public function isAvailable(): bool
    {
        if (! $this->enabled) {
            return false;
        }

        try {
            // Test with a simple TTS request since Piper doesn't have a health endpoint
            $response = Http::timeout(3)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'audio/wav',
                ])
                ->post($this->piperUrl, [
                    'text' => 'test',
                ]);

            return $response->successful() && ! empty($response->body());
        } catch (\Exception $e) {
            Log::debug('TTS service availability check failed: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Generate default queue announcement text
     */
    public function generateQueueAnnouncement(string $queueCode, string $serviceName, string $destinationName): string
    {
        return "Nomor antrian {$queueCode}. Layanan {$serviceName}. Silahkan Menuju {$destinationName}. Terimakasih.";
    }

    /**
     * Generate recall announcement text
     */
    public function generateRecallAnnouncement(string $queueCode, string $serviceName, string $destinationName): string
    {
        return "Nomor antrian {$queueCode}. Layanan {$serviceName}. Silahkan Menuju {$destinationName}. Terimakasih.";
    }

    /**
     * Clean text for better TTS pronunciation
     */
    public function cleanTextForTTS(string $text): string
    {
        // Replace common acronyms and abbreviations
        $replacements = [
            'GEN' => 'General',
            'PHR' => 'Farmasi',
            'LAB' => 'Laboratorium',
            'UGD' => 'Unit Gawat Darurat',
            'KIA' => 'Kesehatan Ibu dan Anak',
            'KB' => 'Keluarga Berencana',
            'DR' => 'Dokter',
            '&' => 'dan',
            '@' => 'at',
        ];

        $cleanText = str_replace(array_keys($replacements), array_values($replacements), $text);

        // Remove special characters that might cause issues
        $cleanText = preg_replace('/[^\p{L}\p{N}\s\-.,!?]/u', ' ', $cleanText);

        // Normalize whitespace
        $cleanText = preg_replace('/\s+/', ' ', trim($cleanText));

        return $cleanText;
    }

    /**
     * Get TTS configuration
     */
    public function getConfig(): array
    {
        return [
            'enabled' => $this->enabled,
            'piper_url' => $this->piperUrl,
            'timeout' => $this->timeout,
            'available' => $this->isAvailable(),
        ];
    }
}
