<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        // Backend Filtering: Ambil parameter tab dari URL
        $currentTab = request('tab', 'all');
        // DEDUPLIKASI: Hanya ambil dari entitas tunggal (NotificationRecipient) agar notifikasi broadcast ke banyak User tidak tampil berulang.
        $baseQuery = \Illuminate\Notifications\DatabaseNotification::where('notifiable_type', 'App\Models\NotificationRecipient');

        $query = clone $baseQuery;
        $query->orderBy('created_at', 'desc');

        if ($currentTab === 'expired') {
            $query->where('data->title', 'LIKE', '%EXPIRED%');
        } elseif ($currentTab === 'urgent') {
            $query->where('data->title', 'LIKE', '%URGENT%');
        } elseif ($currentTab === 'warning') {
            $query->where('data->level', 'warning');
        } elseif ($currentTab === 'reminder') {
            $query->where('data->level', 'info');
        } elseif ($currentTab === 'resolved') {
            $query->where(function($q) {
                $q->where('data->level', 'success')
                  ->orWhere('data->level', 'active');
            });
        } elseif ($currentTab === 'sent') {
            // Tab 'sent' khusus menampilkan riwayat pengiriman sistem tanpa filter spesifik
        }

        // Tarik notifikasi (dengan filter server-side) agar paginate tidak menyebabkan blank page
        $notifications = $query->paginate(15)->withQueryString();

        // Hitung counter global secara tersinkronisasi (bebas duplikat)
        $counts = [
            'all'      => (clone $baseQuery)->count(),
            'expired'  => (clone $baseQuery)->where('data->title', 'LIKE', '%EXPIRED%')->count(),
            'urgent'   => (clone $baseQuery)->where('data->title', 'LIKE', '%URGENT%')->count(),
            'warning'  => (clone $baseQuery)->where('data->level', 'warning')->count(),
            'reminder' => (clone $baseQuery)->where('data->level', 'info')->count(),
            'resolved' => (clone $baseQuery)->where(function($q) {
                              $q->where('data->level', 'success')
                                ->orWhere('data->level', 'active');
                          })->count(),
            'sent'     => (clone $baseQuery)->count(),
        ];

        return view('monitoring.notifications', compact('notifications', 'counts'));
    }

    /**
     * Mark all unread notifications as read.
     */
    public function markAllAsRead(): RedirectResponse
    {
        // Update notifikasi personal (jika ada)
        auth()->user()->unreadNotifications->markAsRead();
        
        // Update notifikasi global sistem agar hilang tanda unread-nya
        \Illuminate\Notifications\DatabaseNotification::where('notifiable_type', 'App\Models\NotificationRecipient')
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return back()->with('success', 'Semua notifikasi telah ditandai sebagai dibaca.');
    }
}
