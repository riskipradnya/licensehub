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
        $periode = $request->input('periode', '3');

        if ($periode === 'custom') {
            $startDate = \Carbon\Carbon::parse($request->input('start_date', now()))->startOfDay();
            $endDate = \Carbon\Carbon::parse($request->input('end_date', now()->addMonths(3)))->endOfDay();
        } else {
            $months = (int) $periode;
            if (!in_array($months, [3, 6, 12])) {
                $months = 3;
                $periode = '3';
            }
            $startDate = now()->startOfDay();
            $endDate = now()->addMonths($months)->endOfDay();
        }

        $baseQuery = License::with('vendor')
            ->where('type', '!=', 'Perpetual')
            ->whereBetween('expiry_date', [$startDate, $endDate]);

        // Kalkulasi Metrik Utama (Grand Total Absolut dari Database)
        $totalProjectedCost = (clone $baseQuery)->sum('cost');
        $grandTotal = $totalProjectedCost; // Alias sesuai permintaan
        $licensesDue = (clone $baseQuery)->count();

        // Kueri terpisah untuk daftar terpaginasi
        $expiringLicenses = (clone $baseQuery)
            ->orderBy('expiry_date', 'asc')
            ->paginate(10)
            ->withQueryString();

        // Kueri untuk Chart (mengambil semua data dalam rentang, tidak terpaginasi)
        $chartLicenses = (clone $baseQuery)->orderBy('expiry_date', 'asc')->get();

        // Persiapan Data Chart.js (Grouping by Month-Year)
        $monthlyCosts = [];
        $currentDate = $startDate->copy()->startOfMonth();
        $endMonth = $endDate->copy()->startOfMonth();
        
        while ($currentDate <= $endMonth) {
            $monthKey = $currentDate->format('M Y');
            $monthlyCosts[$monthKey] = 0;
            $currentDate->addMonth();
        }

        foreach ($chartLicenses as $license) {
            $monthKey = $license->expiry_date->format('M Y');
            if (isset($monthlyCosts[$monthKey])) {
                $monthlyCosts[$monthKey] += $license->cost;
            }
        }

        $totalMonths = max(1, count($monthlyCosts));
        $avgMonthlyCost = $totalProjectedCost / $totalMonths;

        $chartLabels = array_keys($monthlyCosts);
        $chartValues = array_values($monthlyCosts);

        return view('monitoring.cost-projection', compact(
            'expiringLicenses',
            'periode',
            'startDate',
            'endDate',
            'totalProjectedCost',
            'grandTotal',
            'licensesDue',
            'avgMonthlyCost',
            'chartLabels',
            'chartValues'
        ));
    }

    /**
     * Export the cost projection data.
     */
    public function export(Request $request)
    {
        $periode = $request->input('periode', '3');

        if ($periode === 'custom') {
            $startDate = \Carbon\Carbon::parse($request->input('start_date', now()))->startOfDay();
            $endDate = \Carbon\Carbon::parse($request->input('end_date', now()->addMonths(3)))->endOfDay();
        } else {
            $months = (int) $periode;
            if (!in_array($months, [3, 6, 12])) {
                $months = 3;
            }
            $startDate = now()->startOfDay();
            $endDate = now()->addMonths($months)->endOfDay();
        }

        $licenses = License::with('vendor')
            ->where('type', '!=', 'Perpetual')
            ->whereBetween('expiry_date', [$startDate, $endDate])
            ->orderBy('expiry_date', 'asc')
            ->get();

        if ($request->format === 'excel') {
            return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\CostProjectionExport($licenses, $startDate, $endDate), 'cost-projection.xlsx');
        }

        // Default to PDF
        $isExcel = false;
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.cost-projection-pdf', compact('licenses', 'startDate', 'endDate', 'isExcel'));
        return $pdf->download('cost-projection.pdf');
    }
}
