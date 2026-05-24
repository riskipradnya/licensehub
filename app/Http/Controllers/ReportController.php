<?php

namespace App\Http\Controllers;

use App\Models\License;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View|\Illuminate\Http\RedirectResponse
    {
        $year = (int) $request->input('year', now()->year);

        // Ambil daftar tahun unik dari tabel payments
        $availableYears = Payment::where('status', 'paid')
            ->selectRaw('YEAR(payment_date) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year')
            ->toArray();
            
        // Pastikan tahun berjalan selalu ada di dalam array
        if (!in_array(now()->year, $availableYears)) {
            $availableYears[] = now()->year;
            rsort($availableYears);
        }

        // Auto-Redirect Fallback jika $year tidak ada dalam data yang tersedia
        if (!in_array($year, $availableYears)) {
            $fallbackYear = !empty($availableYears) ? $availableYears[0] : now()->year;
            return redirect()->route('reports.index', ['year' => $fallbackYear]);
        }

        // Ambil semua payment lunas di tahun tersebut beserta relasinya
        $payments = Payment::with(['license.vendor', 'license.category'])
            ->where('status', 'paid')
            ->whereYear('payment_date', $year)
            ->get();

        // 1. Total Annual Spend & Total Payments
        $totalAnnualSpend = $payments->sum('amount');
        $totalPayments = $payments->count();

        // 2. Total Vendors (Count vendor unik dari relasi lisensi)
        $totalVendors = $payments->pluck('license.vendor_id')->unique()->filter()->count();

        // 3. Active Licenses
        $activeLicenses = License::where('status', 'active')->count();

        // 4. Monthly Spend (Array 12 indeks)
        $monthlySpend = array_fill(0, 12, 0);
        foreach ($payments as $payment) {
            $monthIndex = (int) $payment->payment_date->format('n') - 1;
            $monthlySpend[$monthIndex] += $payment->amount;
        }


        // 5. Category Spend (Grouping)
        $categories = [];
        foreach ($payments as $payment) {
            if ($payment->license && $payment->license->category) {
                $catName = $payment->license->category->name;
                $categories[$catName] = ($categories[$catName] ?? 0) + $payment->amount;
            }
        }
        arsort($categories);
        $categoryLabels = array_keys($categories);
        $categoryData = array_values($categories);

        // 6. Top Vendors
        $vendorSpend = [];
        foreach ($payments as $payment) {
            if ($payment->license && $payment->license->vendor) {
                $vendorId = $payment->license->vendor->id;
                $vendorName = $payment->license->vendor->name;
                
                if (!isset($vendorSpend[$vendorId])) {
                    $vendorSpend[$vendorId] = [
                        'name' => $vendorName,
                        'spend' => 0,
                        'licenses' => []
                    ];
                }
                
                $vendorSpend[$vendorId]['spend'] += $payment->amount;
                $vendorSpend[$vendorId]['licenses'][] = $payment->license_id;
            }
        }

        // Urutkan vendor berdasarkan spend terbesar
        usort($vendorSpend, function ($a, $b) {
            return $b['spend'] <=> $a['spend'];
        });

        // Ambil Top 5
        $topVendorsRaw = array_slice($vendorSpend, 0, 5);
        $topVendorNames = array_column($topVendorsRaw, 'name');

        // Ambil data tahun lalu untuk perbandingan trend
        $lastYearPayments = Payment::with(['license.vendor'])
            ->where('status', 'paid')
            ->whereYear('payment_date', $year - 1)
            ->whereHas('license.vendor', function($q) use ($topVendorNames) {
                $q->whereIn('name', $topVendorNames);
            })
            ->get();
            
        $lastYearSpend = [];
        foreach ($lastYearPayments as $lyp) {
            $vName = $lyp->license->vendor->name;
            $lastYearSpend[$vName] = ($lastYearSpend[$vName] ?? 0) + $lyp->amount;
        }

        $topVendors = [];
        $rank = 1;

        foreach ($topVendorsRaw as $tv) {
            $pctRaw = $totalAnnualSpend > 0 ? ($tv['spend'] / $totalAnnualSpend) * 100 : 0;
            $pct = number_format($pctRaw, 1) . '%';
            $uniqueLicenses = count(array_unique($tv['licenses']));
            
            $lySpend = $lastYearSpend[$tv['name']] ?? 0;
            // Jika spend tahun ini lebih besar/sama dengan tahun lalu, maka up
            $trend = $tv['spend'] >= $lySpend ? 'up' : 'down';
            
            $topVendors[] = [
                'rank' => $rank++,
                'name' => $tv['name'],
                'licenses' => $uniqueLicenses,
                'spend' => $tv['spend'],
                'pct' => $pct,
                'trend' => $trend,
            ];
        }

        return view('finance.reports', compact(
            'availableYears',
            'year',
            'totalAnnualSpend',
            'totalPayments',
            'totalVendors',
            'activeLicenses',
            'monthlySpend',
            'categoryLabels',
            'categoryData',
            'topVendors'
        ));
    }
}
