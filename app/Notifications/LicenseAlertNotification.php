<?php

namespace App\Notifications;

use App\Models\License;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LicenseAlertNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $license;

    public function __construct(License $license)
    {
        $this->license = $license;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database', \App\Channels\WhatsAppChannel::class];
    }

    private function getCategoryData(): array
    {
        $days = $this->license->days_until_expiry;

        if ($this->license->is_expired || $days <= 0) {
            return ['prefix' => '🔴 EXPIRED', 'level' => 'danger'];
        } elseif ($days <= 7) {
            return ['prefix' => '⚠ URGENT', 'level' => 'danger'];
        } elseif ($days <= 14) {
            return ['prefix' => '⚠️ WARNING', 'level' => 'warning'];
        } else {
            return ['prefix' => '🔔 REMINDER', 'level' => 'info'];
        }
    }

    public function toMail(object $notifiable): MailMessage
    {
        $category = $this->getCategoryData();

        $mail = (new MailMessage)
            ->subject('[' . $category['prefix'] . '] Lisensi ' . $this->license->name . ' Mendekati Expired')
            ->markdown('emails.license-alert', [
                'license' => $this->license,
                'prefix'  => $category['prefix']
            ]);

        $latestInvoice = \App\Models\Invoice::where('license_id', $this->license->id)
                            ->where('status', 'unpaid')
                            ->orderBy('created_at', 'desc')
                            ->first();

        if ($latestInvoice && $latestInvoice->file_path) {
            $path = storage_path('app/public/' . $latestInvoice->file_path);
            if (file_exists($path)) {
                $mail->attach($path);
            }
        }

        return $mail;
    }

    public function toArray(object $notifiable): array
    {
        $category = $this->getCategoryData();

        return [
            'license_id' => $this->license->id,
            'title'      => $category['prefix'] . ': ' . $this->license->name,
            'desc'       => 'Lisensi akan kedaluwarsa dalam ' . $this->license->days_until_expiry . ' hari.',
            'level'      => $category['level'],
            'tab'        => 'sent', 
        ];
    }

    public function toWhatsApp(object $notifiable): array
    {
        $category = $this->getCategoryData();
        
        $cost = $this->license->cost ? number_format($this->license->cost, 0, ',', '.') : '0';
        $billingCycle = $this->license->billing_cycle ?? '-';
        $vendorName = $this->license->vendor ? $this->license->vendor->name : '-';
        $expiryDate = $this->license->expiry_date ? $this->license->expiry_date->format('d M Y') : '-';
        
        $days = $this->license->days_until_expiry;
        $sisaWaktuText = $days < 0 ? "Sudah lewat " . abs($days) . " Hari" : "{$days} Hari Lagi";

        $message = "*[ {$category['prefix']} ]*\n\n";
        $message .= "Halo Tim IT & Finance,\n\n";
        $message .= "Sistem LicenseHub mendeteksi adanya lisensi yang membutuhkan perhatian Anda. Berikut adalah rincian lisensi yang mendekati batas waktu pembayaran:\n\n";
        
        $message .= "Nama Lisensi: *{$this->license->name}*\n";
        $message .= "Vendor: *{$vendorName}*\n";
        $message .= "Biaya: *Rp {$cost} / {$billingCycle}*\n\n";
        
        $message .= "⏳ *Status Waktu:*\n";
        $message .= "Tanggal Kedaluwarsa: {$expiryDate}\n";
        $message .= "Sisa Waktu: *{$sisaWaktuText}*\n\n";
        
        $message .= "Mohon segera jadwalkan perpanjangan (renewal) untuk menghindari gangguan operasional.\n\n";
        
        $message .= "Tinjau Lisensi:\n";
        $message .= url('/licenses/' . $this->license->id) . "\n\n";
        
        $message .= "Terima kasih,\n*Pusat Kendali LicenseHub*";

        $latestInvoice = \App\Models\Invoice::where('license_id', $this->license->id)
                            ->where('status', 'unpaid')
                            ->orderBy('created_at', 'desc')
                            ->first();

        $payload = [
            'message' => $message,
        ];

        // MENGIRIM JALUR FISIK:
        if ($latestInvoice && $latestInvoice->file_path) {
            $path = storage_path('app/public/' . $latestInvoice->file_path);
            
            if (file_exists($path)) {
                $payload['file'] = $path; // Jalur fisik ini akan ditangkap oleh Http::attach()
            }
        }

        return $payload;
    }
}