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
        $notifications = auth()->user()->notifications;

        $counts = [
            'all' => $notifications->count(),
            'expired' => $notifications->filter(fn($n) => isset($n->data['tab']) && $n->data['tab'] === 'expired')->count(),
            'reminder' => $notifications->filter(fn($n) => isset($n->data['tab']) && $n->data['tab'] === 'reminder')->count(),
            'warning' => $notifications->filter(fn($n) => isset($n->data['tab']) && $n->data['tab'] === 'warning')->count(),
            'urgent' => $notifications->filter(fn($n) => isset($n->data['tab']) && $n->data['tab'] === 'urgent')->count(),
            'resolved' => $notifications->filter(fn($n) => isset($n->data['tab']) && $n->data['tab'] === 'resolved')->count(),
            'sent' => $notifications->filter(fn($n) => isset($n->data['tab']) && $n->data['tab'] === 'sent')->count(),
        ];

        return view('monitoring.notifications', compact('notifications', 'counts'));
    }

    /**
     * Mark all unread notifications as read.
     */
    public function markAllAsRead(): RedirectResponse
    {
        auth()->user()->unreadNotifications->markAsRead();
        return back()->with('success', 'Semua notifikasi telah ditandai sebagai dibaca.');
    }
}
