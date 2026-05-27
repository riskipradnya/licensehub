<?php

namespace App\Http\Controllers;

use App\Models\License;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CostProjectionController extends Controller
{
    /**
     * Display the cost projection dashboard.
     */
    public function index(Request $request): View
    {
        // Validasi input bulan (default 6 bulan)
        $months = (int) $request->input('months', 6);
        if (!in_array($months, [3, 6, 12])) {
            $months = 6;
        }

        $startDate = now();
        $endDate = now()->addMonths($months);

        $baseQuery = License::with('vendor')
            ->where('type', '!=', 'Perpetual')
            ->whereBetween('expiry_date', [$startDate, $endDate]);

        // Kalkulasi Metrik Utama (Grand Total Absolut dari Database)
        $totalProjectedCost = (clone $baseQuery)->sum('cost');
        $grandTotal = $totalProjectedCost; // Alias sesuai permintaan
        $licensesDue = (clone $baseQuery)->count();
        $avgMonthlyCost = $months > 0 ? $totalProjectedCost / $months : 0;

        // Kueri terpisah untuk daftar terpaginasi
        $expiringLicenses = (clone $baseQuery)
            ->orderBy('expiry_date', 'asc')
            ->paginate(10)
            ->withQueryString();

        // Kueri untuk Chart (mengambil semua data dalam rentang, tidak terpaginasi)
        $chartLicenses = (clone $baseQuery)->orderBy('expiry_date', 'asc')->get();

        // Persiapan Data Chart.js (Grouping by Month-Year)
        $monthlyCosts = [];
        $currentDate = $startDate->copy();
        for ($i = 0; $i < $months; $i++) {
            $monthKey = $currentDate->format('M Y');
            $monthlyCosts[$monthKey] = 0;
            $currentDate->addMonth();
        }

        foreach ($chartLicenses as $license) {
            $monthKey = $license->expiry_date->format('M Y');
            if (isset($monthlyCosts[$monthKey])) {
                $monthlyCosts[$monthKey] += $license->cost;
            } else {
                $monthlyCosts[$monthKey] = (float) $license->cost;
            }
        }

        $chartLabels = array_keys($monthlyCosts);
        $chartValues = array_values($monthlyCosts);

        return view('monitoring.cost-projection', compact(
            'expiringLicenses',
            'months',
            'totalProjectedCost',
            'grandTotal',
            'licensesDue',
            'avgMonthlyCost',
            'chartLabels',
            'chartValues'
        ));
    }
}
