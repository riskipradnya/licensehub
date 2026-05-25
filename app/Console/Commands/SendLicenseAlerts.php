<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\License;
use App\Models\NotificationRecipient;
use App\Notifications\LicenseAlertNotification;
use Illuminate\Support\Facades\Notification;

class SendLicenseAlerts extends Command
{
    // Nama komando yang akan diketik di terminal
    protected $signature = 'app:send-license-alerts';
    protected $description = 'Cek lisensi dan kirim notifikasi Reminder(31), Warning(14), Urgent(7), atau Expired';

    public function handle()
    {
        $recipients = NotificationRecipient::where('is_active', true)->get();

        if ($recipients->isEmpty()) {
            $this->error('Tidak ada penerima email yang aktif.');
            return;
        }

        // Ambil semua lisensi yang belum dibatalkan
        $licenses = License::where('status', '!=', 'cancelled')->get();
        $sentCount = 0;

        foreach ($licenses as $license) {
            $days = $license->days_until_expiry;

            // BUG FIX: Abaikan lisensi yang tidak punya tanggal expired (infinity/null)
            if ($days === null) {
                continue;
            }

            // ATURAN TRIGGER: H-31, H-14, H-7, atau Expired (<= 0)
            if (in_array($days, [31, 14, 7]) || $days <= 0) {
                Notification::send($recipients, new LicenseAlertNotification($license));
                
                // Ubah status tabel secara otomatis jika mendekati/lewat batas
                if ($days <= 0 && $license->status !== 'expired') {
                    $license->update(['status' => 'expired']);
                } elseif ($days > 0 && $days <= 31 && $license->status === 'active') {
                    $license->update(['status' => 'expiring']);
                }

                $sentCount++;
            }
        }

        $this->info("Operasi selesai. Total $sentCount email alert berhasil dikirim/dimasukkan ke antrean.");
    }
}