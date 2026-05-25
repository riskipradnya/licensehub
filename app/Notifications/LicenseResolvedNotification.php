<?php

namespace App\Notifications;

use App\Models\License;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LicenseResolvedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $license;

    public function __construct(License $license)
    {
        $this->license = $license;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        // Masukkan WhatsAppChannel ke dalam antrean pengiriman bersama Email dan DB
        return ['mail', 'database', \App\Channels\WhatsAppChannel::class];
    }

    /**
     * Get the WhatsApp representation of the notification.
     */
    /**
     * Get the WhatsApp representation of the notification.
     */
    public function toWhatsApp(object $notifiable): array
    {
        // 1. Ambil data vendor dan format tanggal kedaluwarsa baru
        $vendorName = $this->license->vendor ? $this->license->vendor->name : '-';
        $expiryDate = $this->license->expiry_date ? $this->license->expiry_date->format('d F Y') : '-';

        // 2. Rakit teks pesan WhatsApp sesuai format email Anda
        $message = "*Pembaharuan Lisensi Berhasil ✅*\n\n";
        $message .= "Halo Tim IT & Finance,\n\n";
        $message .= "Kabar baik! Pembayaran untuk lisensi *{$this->license->name}* telah berhasil diproses oleh sistem. Operasional infrastruktur dipastikan aman tanpa hambatan.\n\n";
        
        $message .= "*Rincian Lisensi:*\n\n";
        $message .= "Nama: *{$this->license->name}*\n";
        $message .= "Vendor: *{$vendorName}*\n";
        $message .= "Status Baru: *Active (Terpelihara)*\n";
        $message .= "Masa Berlaku Diperpanjang Hingga: *{$expiryDate}*\n\n";
        
        $message .= "Data kelancaran pembayaran ini telah tercatat secara otomatis di dalam sistem LicenseHub.\n\n";
        
        $message .= "Buka Dashboard Lisensi:\n";
        $message .= url('/licenses/' . $this->license->id) . "\n\n";
        
        $message .= "Terima kasih,\n*Pusat Kendali LicenseHub*";

        $latestReceipt = \App\Models\Document::where('license_id', $this->license->id)
                            ->where('document_type', 'Receipt')
                            ->orderBy('created_at', 'desc')
                            ->first();

        $payload = [
            'message' => $message,
        ];

        if ($latestReceipt && $latestReceipt->file_path) {
            if (app()->environment('local')) {
                // Tambahkan parameter 'url' di sini juga!
                $payload['file'] = 'https://docs.fonnte.com/wp-content/uploads/2022/09/Logo-Fonnte-300x72.png';
                $payload['url']  = 'https://docs.fonnte.com/wp-content/uploads/2022/09/Logo-Fonnte-300x72.png';
            } else {
                $payload['file'] = asset('storage/' . $latestReceipt->file_path);
                $payload['url']  = asset('storage/' . $latestReceipt->file_path);
            }
        }

        return $payload;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('[✅ RESOLVED] Lisensi ' . $this->license->name . ' Berhasil Diperpanjang')
            ->markdown('emails.license-resolved', ['license' => $this->license]);

        $latestReceipt = \App\Models\Document::where('license_id', $this->license->id)
                            ->where('document_type', 'Receipt')
                            ->orderBy('created_at', 'desc')
                            ->first();

        if ($latestReceipt && $latestReceipt->file_path) {
            $path = storage_path('app/public/' . $latestReceipt->file_path);
            if (file_exists($path)) {
                $mail->attach($path);
            }
        }

        return $mail;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'license_id' => $this->license->id,
            'title'      => '✅ RESOLVED: ' . $this->license->name,
            'desc'       => 'Lisensi berhasil diperpanjang hingga ' . ($this->license->expiry_date ? $this->license->expiry_date->format('d M Y') : 'tanpa batas waktu') . '.',
            'level'      => 'success',
            'tab'        => 'sent', 
        ];
    }
}