<?php

namespace App\Http\Controllers;

use App\Models\License;
use App\Models\Payment;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the main dashboard with real aggregate data.
     */
    public function index(Request $request): View
    {
        // ── Stat Cards ──────────────────────────────────────────
        $activeLicenses  = License::where('status', 'active')->count();
        $expiringSoon    = License::where('status', 'expiring')->count();
        $expiredLicenses = License::where('status', 'expired')->count();
        $totalMonthlyCost = (float) License::whereIn('status', ['active', 'expiring'])->sum('cost');

        // ── Expiring Soon Table (licenses expiring within 31 days) ──
        $expiringLicenses = License::with(['vendor', 'category'])
            ->whereNotNull('expiry_date')
            ->whereBetween('expiry_date', [now(), now()->addDays(31)])
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

        // ── Chart Data (Historical Cost Trend) ─────────
        $filter = $request->query('filter', '6_months');
        
        $monthsToSub = match($filter) {
            '1_month'   => 1,
            '12_months' => 12,
            default     => 6,
        };

        $startDate = now()->subMonths($monthsToSub)->startOfMonth();
        $endDate = now()->endOfMonth();

        // Ambil data dari database yang expired di rentang waktu tersebut
        $licensesInPeriod = License::whereNotNull('expiry_date')
            ->whereBetween('expiry_date', [$startDate, $endDate])
            ->get();

        $chartLabels = [];
        $chartValues = [];

        // Buat urutan bulan secara berurutan (chronological) dari terlama ke terbaru
        for ($i = $monthsToSub - 1; $i >= 0; $i--) {
            $monthObj = now()->subMonths($i);
            $label = $monthObj->format('M Y'); // e.g. 'Jan 2026'
            
            $chartLabels[] = $label;
            
            // Cari total cost untuk bulan dan tahun ini
            $monthlyTotal = $licensesInPeriod->filter(function($lic) use ($monthObj) {
                return Carbon::parse($lic->expiry_date)->format('Y-m') === $monthObj->format('Y-m');
            })->sum('cost');

            // Format ke dalam jutaan jika angkanya besar, tapi sementara keep raw value biar view mudah
            $chartValues[] = (float) $monthlyTotal;
        }

        return view('dashboard.index', compact(
            'activeLicenses',
            'expiringSoon',
            'expiredLicenses',
            'totalMonthlyCost',
            'expiringLicenses',
            'alerts',
            'chartLabels',
            'chartValues',
            'filter'
        ));
    }
}
