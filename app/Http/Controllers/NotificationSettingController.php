<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationSettingController extends Controller
{
    public function index()
    {
        $recipients = \App\Models\NotificationRecipient::all();
        return view('settings.notifications.index', compact('recipients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:notification_recipients,email',
            'whatsapp' => 'nullable|string|max:255',
        ]);

        \App\Models\NotificationRecipient::create($validated);

        return redirect()->back()->with('success', 'Penerima notifikasi berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $recipient = \App\Models\NotificationRecipient::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:notification_recipients,email,' . $recipient->id,
            'whatsapp' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        // Default to false if not present in the request (e.g. checkbox unchecked)
        $validated['is_active'] = $request->has('is_active');

        $recipient->update($validated);

        return redirect()->back()->with('success', 'Penerima notifikasi berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $recipient = \App\Models\NotificationRecipient::findOrFail($id);
        $recipient->delete();

        return redirect()->back()->with('success', 'Penerima notifikasi berhasil dihapus.');
    }
}
