<?php

namespace App\Notifications;

use App\Models\License;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LicenseExpiringNotification extends Notification
{
    use Queueable;

    protected $license;
    protected $daysRemaining;
    protected $level;

    /**
     * Create a new notification instance.
     */
    public function __construct(License $license, int $daysRemaining, string $level)
    {
        $this->license = $license;
        $this->daysRemaining = $daysRemaining;
        $this->level = $level;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $label = '';
        if ($this->daysRemaining > 0) { 
            $label = 'H-' . $this->daysRemaining; 
        } elseif ($this->daysRemaining === 0) { 
            $label = 'Hari Ini'; 
        } else { 
            $label = 'H+' . abs($this->daysRemaining); 
        }

        $desc = $this->daysRemaining < 0 
            ? "Lisensi {$this->license->name} telah kedaluwarsa sejak " . abs($this->daysRemaining) . " hari yang lalu."
            : "Lisensi {$this->license->name} akan kedaluwarsa dalam {$this->daysRemaining} hari.";

        if ($this->level === 'active') {
            $desc = "Lisensi {$this->license->name} berhasil diperpanjang.";
        }

        return [
            'license_id' => $this->license->id,
            'title'      => $this->license->name,
            'desc'       => $desc,
            'level'      => $this->level,
            'label'      => $this->level === 'active' ? 'RESOLVED' : $label,
            'tab'        => $this->level === 'active' ? 'resolved' : (
                                $this->level === 'info' ? 'reminder' : (
                                    $this->level === 'warning' ? 'warning' : (
                                        $this->level === 'critical' ? 'expired' : 'urgent'
                                    )
                                )
                            ),
        ];
    }
}
