<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppChannel
{
    public function send(object $notifiable, Notification $notification): void
    {
        if (! method_exists($notification, 'toWhatsApp')) {
            return;
        }

        $to = $notifiable->routeNotificationFor('whatsapp') ?? $notifiable->whatsapp;

        if (! $to) {
            return;
        }

        $data = $notification->toWhatsApp($notifiable);
        $apiUrl = env('WA_API_URL');
        $apiToken = env('WA_API_TOKEN');

        if (! $apiUrl || ! $apiToken) {
            Log::error('Fonnte API URL atau Token belum disetting di .env');
            return;
        }

        try {
            // MERAKIT PAYLOAD INTI (Target & Pesan Utama WAJIB ada)
            $payload = [
                'target'  => $to,
                'message' => $data['message'],
            ];

            // SIAPKAN PELUNCUR HTTP (Toleransi Timeout 30 detik untuk Hosting)
            $request = Http::timeout(30)
                ->withoutVerifying()
                ->withHeaders([
                    'Authorization' => $apiToken
                ]);

            // EKSEKUSI PENGIRIMAN: Gunakan parameter URL publik agar Fonnte Server yang mendownload sendiri
            if (isset($data['url'])) {
                Log::info('Fonnte: Mengirim SATU PAKET (Teks + URL Link)...', ['url' => $data['url']]);
                $payload['url'] = $data['url'];
            } else {
                Log::info('Fonnte: Mengirim Teks Saja (Tanpa Lampiran)...');
            }

            // TEMBAKKAN SEMUANYA SEKALIGUS
            $response = $request->post($apiUrl, $payload);

            // KAMERA PENGAWAS AKHIR
            if ($response->successful()) {
                Log::info('Fonnte API Berhasil: ' . $response->body());
            } else {
                Log::error('Fonnte API Gagal: ' . $response->body());
            }

        } catch (\Exception $e) {
            Log::error('WhatsAppChannel Error: ' . $e->getMessage());
        }
    }
}