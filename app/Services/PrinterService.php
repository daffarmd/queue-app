<?php

namespace App\Services;

use App\Models\Queue;
use Exception;
use Illuminate\Support\Facades\Log;

class PrinterService
{
    private bool $isConnected = false;

    private ?string $printerPath = null;

    public function printQueueTicket(Queue $queue): bool
    {
        try {
            // Attempt to connect to printer if not already connected
            if (! $this->isConnected && ! $this->connectToPrinter()) {
                Log::warning('Printer connection failed for queue: '.$queue->code);

                return false;
            }

            $ticketData = $this->formatTicket($queue);

            // Send print command
            return $this->sendToPrinter($ticketData);

        } catch (Exception $e) {
            Log::error('Print failed for queue '.$queue->code.': '.$e->getMessage());

            return false;
        }
    }

    private function connectToPrinter(): bool
    {
        try {
            // Try to detect USB thermal printer
            $usbPrinters = [
                '/dev/usb/lp0',
                '/dev/lp0',
                '/dev/ttyUSB0',
            ];

            foreach ($usbPrinters as $path) {
                if (file_exists($path) && is_writable($path)) {
                    $this->printerPath = $path;
                    $this->isConnected = true;
                    Log::info('Printer connected via USB: '.$path);

                    return true;
                }
            }

            // Try Bluetooth printers (simplified detection)
            // In a real implementation, you'd use proper Bluetooth APIs
            $bluetoothPaths = [
                '/dev/rfcomm0',
                '/dev/ttyS0',
            ];

            foreach ($bluetoothPaths as $path) {
                if (file_exists($path) && is_writable($path)) {
                    $this->printerPath = $path;
                    $this->isConnected = true;
                    Log::info('Printer connected via Bluetooth: '.$path);

                    return true;
                }
            }

            return false;

        } catch (Exception $e) {
            Log::error('Printer connection error: '.$e->getMessage());

            return false;
        }
    }

    private function formatTicket(Queue $queue): string
    {
        $timestamp = now()->setTimezone('Asia/Jakarta')->format('Y-m-d H:i:s');

        return "
        ================================
                TRI MULYO
              Queue System
        ================================

        Queue Number: {$queue->code}
        Patient: {$queue->patient_name}
        Service: {$queue->service->name}
        Counter: {$queue->counter}
        Date/Time: {$timestamp}

        ================================
        Please wait for your number
        to be called
        ================================


        ";
    }

    private function sendToPrinter(string $data): bool
    {
        try {
            if (! $this->printerPath) {
                return false;
            }

            // ESC/POS commands for thermal printer
            $escpos = "\x1B\x40"; // Initialize printer
            $escpos .= "\x1B\x61\x01"; // Center align
            $escpos .= $data;
            $escpos .= "\x1D\x56\x41\x03"; // Cut paper

            $handle = fopen($this->printerPath, 'w');
            if ($handle === false) {
                return false;
            }

            $result = fwrite($handle, $escpos);
            fclose($handle);

            return $result !== false;

        } catch (Exception $e) {
            Log::error('Print send error: '.$e->getMessage());

            return false;
        }
    }

    public function isConnected(): bool
    {
        return $this->isConnected;
    }

    public function disconnect(): void
    {
        $this->isConnected = false;
        $this->printerPath = null;
    }
}
