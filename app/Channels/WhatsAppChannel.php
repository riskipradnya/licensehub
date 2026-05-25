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

            // SIAPKAN PELUNCUR HTTP
            $request = Http::withHeaders([
                'Authorization' => $apiToken
            ]);

            // EKSEKUSI SATU PAKET: Sisipkan file fisik beserta pesan utamanya
            if (isset($data['file']) && file_exists($data['file'])) {
                Log::info('Fonnte: Mengirim SATU PAKET (Teks + PDF Lokal)...', ['path' => $data['file']]);
                
                // attach() mengubah request jadi multipart/form-data
                $request->attach('file', file_get_contents($data['file']), 'invoice.pdf');
                
            } elseif (isset($data['url'])) {
                Log::info('Fonnte: Mengirim SATU PAKET (Teks + URL Link)...', ['url' => $data['url']]);
                
                // Jika pakai link publik, cukup tambahkan ke dalam array payload
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