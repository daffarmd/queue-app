<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Text-to-Speech Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Piper TTS integration. This system uses Piper TTS
    | server for generating voice announcements in real-time.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | TTS Service Settings
    |--------------------------------------------------------------------------
    */
    'enabled' => env('TTS_ENABLED', true),

    'piper_url' => env('TTS_PIPER_URL', 'http://127.0.0.1:5000'),

    'timeout' => env('TTS_TIMEOUT', 10),

    /*
    |--------------------------------------------------------------------------
    | Voice Settings
    |--------------------------------------------------------------------------
    */
    'default_voice' => env('TTS_DEFAULT_VOICE', 'id'),

    'speech_rate' => env('TTS_SPEECH_RATE', 1.0),

    'volume' => env('TTS_VOLUME', 0.8),

    /*
    |--------------------------------------------------------------------------
    | Queue Announcement Templates
    |--------------------------------------------------------------------------
    */
    'templates' => [
        'queue_called' => 'Nomor antrian :code. Layanan :service. Silahkan Menuju :destination. Terimakasih.',
        'queue_recalled' => 'Nomor antrian :code. Layanan :service. Silahkan Menuju :destination. Terimakasih.',
        'queue_skipped' => 'Nomor antrian :code telah dilewati',
        'service_announcement' => 'Pengumuman untuk layanan :service',
    ],

    /*
    |--------------------------------------------------------------------------
    | Audio Settings
    |--------------------------------------------------------------------------
    */
    'audio_format' => 'wav',
    'sample_rate' => 22050,
    'channels' => 1,

    /*
    |--------------------------------------------------------------------------
    | Fallback Settings
    |--------------------------------------------------------------------------
    */
    'fallback_to_browser' => env('TTS_FALLBACK_BROWSER', true),
    'cache_audio' => env('TTS_CACHE_AUDIO', false),
    'cache_duration' => 3600, // 1 hour in seconds
];
