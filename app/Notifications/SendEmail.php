<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendEmail extends Notification
{
    use Queueable;

    // ✅ PROPERTI UNTUK DATA DINAMIS
    protected $subject;
    protected $messageLine;
    protected $actionText;
    protected $actionUrl;

    /**
     * Buat instance notifikasi baru.
     */
    public function __construct(string $subject, string $messageLine, string $actionText = null, string $actionUrl = null)
    {
        $this->subject = $subject;
        $this->messageLine = $messageLine;
        $this->actionText = $actionText;
        $this->actionUrl = $actionUrl;
    }

    /**
     * Dapatkan saluran pengiriman notifikasi.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Dapatkan representasi email dari notifikasi.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            // ✅ Menggunakan subjek dinamis
            ->subject($this->subject) 
            ->greeting('Halo, ' . $notifiable->name . '!')
            // ✅ Menggunakan isi pesan dinamis
            ->line($this->messageLine);

        // Tambahkan tombol Aksi jika ada
        if ($this->actionText && $this->actionUrl) {
            $mail->action($this->actionText, url($this->actionUrl));
        }

        return $mail->line('Terima kasih telah menggunakan aplikasi kami!');
    }

    /**
     * Dapatkan representasi array dari notifikasi.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'subject' => $this->subject,
            'message' => $this->messageLine,
        ];
    }
}