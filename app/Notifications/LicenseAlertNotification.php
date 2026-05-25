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
        return ['mail', 'database'];
    }

    // Fungsi bantuan untuk menentukan kategori berdasarkan sisa hari
    // Fungsi bantuan untuk menentukan kategori berdasarkan sisa hari
    private function getCategoryData(): array
    {
        $days = $this->license->days_until_expiry;

        // 1. Kategori EXPIRED (Sudah kedaluwarsa atau sisa hari <= 0)
        if ($this->license->is_expired || $days <= 0) {
            return ['prefix' => '🔴 EXPIRED', 'level' => 'danger'];
        } 
        // 2. Kategori URGENT (Sisa 1 sampai 7 hari)
        elseif ($days <= 7) {
            return ['prefix' => '⚠ URGENT', 'level' => 'danger'];
        } 
        // 3. Kategori WARNING (Sisa 8 sampai 14 hari)
        elseif ($days <= 14) {
            return ['prefix' => '⚠️ WARNING', 'level' => 'warning'];
        } 
        // 4. Kategori REMINDER (Sisa hari di atas 14 hari, misal: 31 hari)
        else {
            return ['prefix' => '🔔 REMINDER', 'level' => 'info'];
        }
    }

    public function toMail(object $notifiable): MailMessage
    {
        $category = $this->getCategoryData();

        return (new MailMessage)
            ->subject('[' . $category['prefix'] . '] Lisensi ' . $this->license->name . ' Mendekati Expired')
            ->markdown('emails.license-alert', [
                'license' => $this->license,
                'prefix'  => $category['prefix']
            ]);
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
}