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
        $personalNotifications = auth()->user()->notifications;

        $sentTotalCount = \Illuminate\Notifications\DatabaseNotification::where('data->tab', 'sent')->count();

        $sentNotifications = \Illuminate\Notifications\DatabaseNotification::where('data->tab', 'sent')
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        $counts = [
            'all' => $personalNotifications->count(),
            'expired' => $personalNotifications->filter(fn($n) => isset($n->data['tab']) && $n->data['tab'] === 'expired')->count(),
            'reminder' => $personalNotifications->filter(fn($n) => isset($n->data['tab']) && $n->data['tab'] === 'reminder')->count(),
            'warning' => $personalNotifications->filter(fn($n) => isset($n->data['tab']) && $n->data['tab'] === 'warning')->count(),
            'urgent' => $personalNotifications->filter(fn($n) => isset($n->data['tab']) && $n->data['tab'] === 'urgent')->count(),
            'resolved' => $personalNotifications->filter(fn($n) => isset($n->data['tab']) && $n->data['tab'] === 'resolved')->count(),
            'sent' => $sentTotalCount,
        ];

        $notifications = $personalNotifications;

        return view('monitoring.notifications', compact('notifications', 'sentNotifications', 'counts'));
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
