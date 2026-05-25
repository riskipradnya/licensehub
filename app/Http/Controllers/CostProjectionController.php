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

        // Ambil lisensi yang bukan Perpetual dan akan kedaluwarsa dalam rentang waktu yang dipilih
        $licenses = License::with('vendor')
            ->where('type', '!=', 'Perpetual')
            ->whereBetween('expiry_date', [$startDate, $endDate])
            ->orderBy('expiry_date', 'asc')
            ->get();

        // Kalkulasi Metrik Utama
        $totalProjectedCost = $licenses->sum('cost');
        $licensesDue = $licenses->count();
        $avgMonthlyCost = $months > 0 ? $totalProjectedCost / $months : 0;

        // Persiapan Data Chart.js (Grouping by Month-Year)
        // Array untuk menyimpan total biaya per bulan
        $monthlyCosts = [];

        // Inisialisasi array untuk semua bulan dalam rentang untuk memastikan chart tidak bolong
        $currentDate = $startDate->copy();
        for ($i = 0; $i < $months; $i++) {
            $monthKey = $currentDate->format('M Y');
            $monthlyCosts[$monthKey] = 0;
            $currentDate->addMonth();
        }

        // Akumulasi biaya ke bulan yang sesuai
        foreach ($licenses as $license) {
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
            'licenses',
            'months',
            'totalProjectedCost',
            'licensesDue',
            'avgMonthlyCost',
            'chartLabels',
            'chartValues'
        ));
    }
}
