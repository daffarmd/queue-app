<?php

namespace App\Console\Commands;

use App\Services\TTSService;
use Illuminate\Console\Command;

class TTSTestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tts:test {text?} {--save} {--play}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Piper TTS functionality and generate sample audio';

    /**
     * Execute the console command.
     */
    public function handle(TTSService $ttsService): int
    {
        $this->info('🎤 Testing Piper TTS Service...');

        // Check TTS service availability
        $config = $ttsService->getConfig();

        $this->table(['Setting', 'Value'], [
            ['Enabled', $config['enabled'] ? '✅ Yes' : '❌ No'],
            ['Piper URL', $config['piper_url']],
            ['Available', $config['available'] ? '✅ Connected' : '❌ Offline'],
            ['Timeout', $config['timeout'].'s'],
        ]);

        if (! $config['available']) {
            $this->error('❌ Piper TTS server is not available at '.$config['piper_url']);
            $this->info('💡 Make sure Piper TTS server is running on port 5000');

            return 1;
        }

        // Get text to test
        $text = $this->argument('text') ?: $this->choice(
            'Select test message:',
            [
                'Selamat datang di Klinik TRI MULYO',
                'Nomor antrian GEN-001, layanan General, silakan menuju ke Reception',
                'Nomor antrian PHR-002, layanan Farmasi, silakan kembali ke Apotek',
                'Pengumuman untuk semua pasien, mohon menunggu dengan sabar',
            ],
            0
        );

        $this->info("🔊 Generating TTS for: \"$text\"");

        // Generate audio
        $audioData = $ttsService->generateSpeech($text);

        if (! $audioData) {
            $this->error('❌ Failed to generate TTS audio');

            return 1;
        }

        $this->info('✅ TTS audio generated successfully ('.strlen($audioData).' bytes)');

        // Save to file if requested
        if ($this->option('save')) {
            $filename = 'tts_test_'.date('Y-m-d_H-i-s').'.wav';
            $filepath = storage_path('app/public/'.$filename);

            if (! file_exists(dirname($filepath))) {
                mkdir(dirname($filepath), 0755, true);
            }

            file_put_contents($filepath, $audioData);
            $this->info("💾 Audio saved to: $filepath");
        }

        // Test different message types
        $this->info('🧪 Testing queue announcement templates...');

        $testCases = [
            ['GEN-001', 'General Consultation', 'Reception', 'called'],
            ['PHR-002', 'Pharmacy', 'Apotek', 'recalled'],
            ['LAB-003', 'Laboratory', 'Lab Room', 'called'],
        ];

        foreach ($testCases as [$code, $service, $destination, $type]) {
            $template = config("tts.templates.queue_{$type}");
            $message = str_replace(
                [':code', ':service', ':destination'],
                [$code, $service, $destination],
                $template
            );

            $cleanMessage = $ttsService->cleanTextForTTS($message);

            $this->line("📝 {$type}: {$cleanMessage}");

            // Test generation
            $testAudio = $ttsService->generateSpeech($cleanMessage);
            if ($testAudio) {
                $this->info('✅ Generated ('.strlen($testAudio).' bytes)');
            } else {
                $this->error('❌ Failed to generate');
            }
        }

        $this->info('🎉 TTS testing completed!');

        // Show usage examples
        $this->newLine();
        $this->info('📚 Usage Examples:');
        $this->line('• php artisan tts:test "Your custom message"');
        $this->line('• php artisan tts:test --save (saves audio to storage/app/public/)');
        $this->line('• Test via web: GET /tts/test');
        $this->line('• Check health: GET /tts/health');

        return 0;
    }
}
