<?php

namespace App\Http\Controllers;

use App\Models\License;
use App\Models\Payment;
use App\Models\Vendor;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the main dashboard with real aggregate data.
     */
    public function index(): View
    {
        // ── Stat Cards ──────────────────────────────────────────
        $activeLicenses  = License::where('status', 'active')->count();
        $expiringSoon    = License::where('status', 'expiring')->count();
        $expiredLicenses = License::where('status', 'expired')->count();
        $totalMonthlyCost = (float) License::whereIn('status', ['active', 'expiring'])->sum('cost');

        // ── Expiring Soon Table (licenses expiring within 60 days) ──
        $expiringLicenses = License::with(['vendor', 'category'])
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '>=', now())
            ->where('expiry_date', '<=', now()->addDays(60))
            ->orderBy('expiry_date')
            ->limit(10)
            ->get();

        // ── Recent Alerts (most urgent licenses) ────────────────
        $alerts = License::with('vendor')
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '>=', now()->subDays(7))
            ->where('expiry_date', '<=', now()->addDays(30))
            ->orderBy('expiry_date')
            ->limit(5)
            ->get()
            ->map(function ($license) {
                $daysLeft = $license->days_until_expiry;
                return [
                    'name'    => $license->name,
                    'message' => $daysLeft <= 0
                        ? 'Kedaluwarsa ' . abs($daysLeft) . ' hari lalu'
                        : "H-{$daysLeft} — " . $license->expiry_date->format('d M Y'),
                    'variant' => $daysLeft <= 0 ? 'danger' : ($daysLeft <= 7 ? 'danger' : 'warning'),
                    'time'    => $license->updated_at->diffForHumans(short: true),
                ];
            });

        // ── Chart Data (monthly cost for last 6 months) ─────────
        $chartLabels = [];
        $chartData   = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $chartLabels[] = $month->format('M');

            // Sum cost of licenses active during that month
            $monthlyCost = License::whereIn('status', ['active', 'expiring', 'expired'])
                ->where('start_date', '<=', $month->endOfMonth())
                ->where(function ($q) use ($month) {
                    $q->whereNull('expiry_date')
                      ->orWhere('expiry_date', '>=', $month->startOfMonth());
                })
                ->sum('cost');

            $chartData[] = round((float) $monthlyCost / 1000000, 1); // In millions
        }

        return view('dashboard.index', compact(
            'activeLicenses',
            'expiringSoon',
            'expiredLicenses',
            'totalMonthlyCost',
            'expiringLicenses',
            'alerts',
            'chartLabels',
            'chartData',
        ));
    }
}
