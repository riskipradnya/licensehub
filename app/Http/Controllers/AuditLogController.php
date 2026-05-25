<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Activitylog\Models\Activity;

class AuditLogController extends Controller
{
    /**
     * Display the audit logs.
     */
    public function index(): View
    {
        // Ambil semua aktivitas, muat relasi causer dan subject, urutkan terbaru
        $activities = Activity::with(['causer', 'subject'])
            ->latest()
            ->paginate(15);

        return view('monitoring.audit-log', compact('activities'));
    }
}
