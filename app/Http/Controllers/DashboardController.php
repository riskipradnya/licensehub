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
        // ── Date Logic & Filter ─────────
        $filter = $request->query('filter', '6_months');
        
        if ($filter === 'custom' && $request->has(['start_date', 'end_date'])) {
            $startDate = Carbon::parse($request->query('start_date'))->startOfDay();
            $endDate = Carbon::parse($request->query('end_date'))->endOfDay();
        } else {
            $monthsToAdd = match($filter) {
                '3_months'  => 3,
                '6_months'  => 6,
                '12_months' => 12,
                default     => 6,
            };
            $startDate = now()->startOfMonth();
            $endDate = now()->addMonths($monthsToAdd)->endOfMonth();
        }

        // ── Stat Cards ──────────────────────────────────────────
        // Stat Cards remain global point-in-time metrics and are unaffected by chart date filters
        $activeLicenses  = License::where('status', 'active')->count();
        $expiringSoon    = License::where('status', 'expiring')->count();
        $expiredLicenses = License::where('status', 'expired')->count();
        
        $totalPerpetualCost = (float) License::whereIn('status', ['active', 'expiring'])
            ->where('billing_cycle', 'one_time')
            ->sum('cost');

        $annualRecurringCost = (float) License::whereIn('status', ['active', 'expiring'])
            ->where('billing_cycle', '!=', 'one_time')
            ->get()
            ->sum(function ($license) {
                return match($license->billing_cycle) {
                    'monthly'   => $license->cost * 12,
                    'quarterly' => $license->cost * 4,
                    'yearly'    => $license->cost,
                    default     => 0,
                };
            });

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
        $licensesInPeriod = License::whereNotNull('expiry_date')
            ->whereBetween('expiry_date', [$startDate, $endDate])
            ->get();

        $chartLabels = [];
        $chartValues = [];

        // Buat urutan bulan secara berurutan (chronological) dari terlama ke terbaru
        $startPeriod = $startDate->copy()->startOfMonth();
        $endPeriod = $endDate->copy()->startOfMonth();
        
        while ($startPeriod <= $endPeriod) {
            $label = $startPeriod->format('M Y');
            $chartLabels[] = $label;
            
            $monthlyTotal = $licensesInPeriod->filter(function($lic) use ($startPeriod) {
                return Carbon::parse($lic->expiry_date)->format('Y-m') === $startPeriod->format('Y-m');
            })->sum('cost');
            
            $chartValues[] = (float) $monthlyTotal;
            
            $startPeriod->addMonth();
        }

        // Pass dates back to view for the date picker
        $startDateStr = $startDate->format('Y-m-d');
        $endDateStr = $endDate->format('Y-m-d');

        return view('dashboard.index', compact(
            'activeLicenses',
            'expiringSoon',
            'expiredLicenses',
            'totalPerpetualCost',
            'annualRecurringCost',
            'expiringLicenses',
            'alerts',
            'chartLabels',
            'chartValues',
            'filter',
            'startDateStr',
            'endDateStr'
        ));
    }
}
