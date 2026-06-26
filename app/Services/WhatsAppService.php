<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected $endpoint;
    protected $apiKey;

    public function __construct()
    {
        $settings = Setting::whereIn('key', ['wa_api_endpoint', 'wa_api_key'])
                           ->pluck('value', 'key');
                           
        // Default Fonnte Endpoint jika di DB kosong
        $this->endpoint = $settings['wa_api_endpoint'] ?? 'https://api.fonnte.com/send';
        $this->apiKey = $settings['wa_api_key'] ?? null;
    }

    public function sendNotification(string $toPhoneNumber, string $message): bool
    {
        if (empty($this->endpoint) || empty($this->apiKey)) {
            Log::warning("WhatsApp API settings are incomplete.");
            return false;
        }
        
        // 1. Bersihkan nomor (Fonnte minta angka saja, mulai dengan 62)
        $cleanNumber = preg_replace('/[^0-9]/', '', $toPhoneNumber);
        if (str_starts_with($cleanNumber, '0')) {
            $cleanNumber = '62' . substr($cleanNumber, 1);
        }
        
        try {
            // 2. Kirim dengan format yang diminta Fonnte
            $response = Http::withoutVerifying()->withHeaders([
                'Authorization' => $this->apiKey, // Fonnte minta token di sini
            ])->asForm()->post($this->endpoint, [
                'target' => $cleanNumber, // Fonnte pakai 'target', bukan 'to'
                'message' => $message,
            ]);

            // 3. Cek respon asli dari Fonnte
            $result = $response->json();

            //dd($result);

            if ($response->successful() && isset($result['status']) && $result['status'] == true) {
                Log::info("WhatsApp Sent: " . $cleanNumber);
                sleep(1);
                return true;
            }

            Log::error("Fonnte Error: " . ($result['reason'] ?? $response->body()));
            sleep(1);
            return false;

            

        } catch (\Exception $e) {
            // Ini akan menangkap jika cURL di Laragon mati
            Log::error("WhatsApp Connection Error: " . $e->getMessage());
            return false;
        }
    }
}