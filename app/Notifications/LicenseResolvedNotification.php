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

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('[✅ RESOLVED] Lisensi ' . $this->license->name . ' Berhasil Diperpanjang')
            ->markdown('emails.license-resolved', ['license' => $this->license]);
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