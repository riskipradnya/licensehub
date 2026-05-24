<?php

namespace App\Console\Commands;

use App\Models\License;
use App\Models\User;
use App\Notifications\LicenseExpiringNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class CheckExpiringLicenses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-expiring-licenses {--test}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cek lisensi yang akan expired dan kirim notifikasi.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Ambil user terkait yang berhak menerima notif ini
        $users = User::whereIn('role', ['super_admin', 'it_manager', 'finance_manager', 'it_staff'])
            ->where('is_active', 1)
            ->get();

        if ($this->option('test')) {
            $license = License::first(); // Ambil 1 lisensi sembarang untuk testing
            if ($license) {
                // Kirim 2 notifikasi simulasi secara paksa ke semua user target
                \Illuminate\Support\Facades\Notification::send($users, new LicenseExpiringNotification($license, 1, 'danger'));
                \Illuminate\Support\Facades\Notification::send($users, new LicenseExpiringNotification($license, 14, 'warning'));
                $this->info("Mode Simulasi Aktif: 2 Notifikasi dummy (Urgent & Warning) berhasil dikirim!");
            } else {
                $this->error("Gagal simulasi: Silakan buat minimal 1 lisensi terlebih dahulu di database.");
            }
            return;
        }

        $licenses = License::whereIn('status', ['active', 'expiring', 'expired'])->get();

        foreach ($licenses as $license) {
            if (!$license->expiry_date) {
                continue;
            }

            // Hitung sisa hari (false agar menghasilkan nilai negatif jika sudah lewat)
            $days = (int) now()->startOfDay()->diffInDays($license->expiry_date->startOfDay(), false);

            $level = null;

            if ($days <= 31 && $days > 14) {
                $level = 'info';
                $tab = 'reminder';
                // Jika masih active dan sudah masuk masa tenggang, ubah ke expiring
                if ($license->status === 'active') {
                    $license->update(['status' => 'expiring']);
                }
            } elseif ($days <= 14 && $days > 7) {
                $level = 'warning';
                if ($license->status === 'active') {
                    $license->update(['status' => 'expiring']);
                }
            } elseif ($days <= 7 && $days > 0) {
                $level = 'danger';
            } elseif ($days <= 0) {
                $level = 'critical';
                if ($license->status !== 'expired') {
                    $license->update(['status' => 'expired']);
                }
            }

            if ($level) {
                \Illuminate\Support\Facades\DB::table('notifications')
                    ->where('data', 'like', '%"license_id":"'.$license->id.'"%')
                    ->whereNull('read_at')
                    ->delete();

                Notification::send($users, new LicenseExpiringNotification($license, $days, $level));
                $this->info("Notifikasi dikirim untuk {$license->name} (Level: {$level}, Sisa: {$days} hari)");
            }
        }

        $this->info('Pengecekan expiry lisensi selesai.');
    }
}
