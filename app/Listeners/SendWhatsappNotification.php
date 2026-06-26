<?php

namespace App\Listeners;

use App\Models\ParentModel;
use App\Events\AttendanceRecorded;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendWhatsappNotification implements ShouldQueue // Gunakan Queue agar tidak memperlambat proses absensi
{
    use InteractsWithQueue;

    public function handle(AttendanceRecorded $event)
    {
        $absence = $event->absence;
        $student = $absence->student;

        // 1. Ambil Orang Tua yang Terhubung
        $parents = $student->parents;

        foreach ($parents as $parent) {
            $phoneNumber = $parent->phone_number;
            $parentName = $parent->name;
            
            // Format Pesan
            $message = sprintf(
                "Yth. Bapak/Ibu %s, Siswa/i %s (%s) telah tercatat ABSENSI pada pukul %s dengan status **%s**.\nMohon diperhatikan. \n(Sistem E-Absensi Sekolah)",
                $parentName,
                $student->name,
                $student->class->name ?? 'N/A',
                $absence->attendance_time->format('H:i'),
                $absence->status
            );
            
            // 2. KIRIM VIA WHATSAPP API
            // Ini adalah contoh placeholder. Implementasi nyata bergantung pada layanan yang Anda gunakan (e.g., Twilio, Infobip, WA Gateway API).
            $this->sendWhatsApp($phoneNumber, $message);
        }
    }

    private function sendWhatsApp(string $to, string $text)
    {
        // LOGIC INTEGRASI API WA DI SINI (e.g., menggunakan Guzzle HTTP client)
        // Contoh:
        // $client = new \GuzzleHttp\Client();
        // $response = $client->post('https://api.wagateway.com/send', [
        //     'form_params' => [
        //         'api_key' => env('WA_API_KEY'),
        //         'number' => $to,
        //         'message' => $text,
        //     ]
        // ]);
        
        Log::info("WA Notifikasi terkirim ke $to: $text");
    }
}