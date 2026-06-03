<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Annual Financial Report {{ $year }}</title>
    <style>
        /* Base Reset & Fonts */
        @page { margin: 40px 50px; }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            line-height: 1.5;
            color: #333333;
            margin: 0;
            padding: 0;
        }

        /* Colors & Typography */
        h1, h2, h3 { color: #1e3a8a; margin-top: 0; }
        h1 { font-size: 22px; margin-bottom: 5px; }
        h2 { font-size: 16px; margin-bottom: 10px; margin-top: 25px; border-bottom: 1px solid #e2e8f0; padding-bottom: 5px; }
        
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-muted { color: #64748b; }
        
        /* Header Styling */
        .header-table { width: 100%; border-bottom: 2px solid #1e3a8a; padding-bottom: 15px; margin-bottom: 20px; }
        .header-logo { font-size: 24px; font-weight: bold; color: #1e3a8a; }
        .header-title { text-align: right; }

        /* Summary Boxes (Tables) */
        .summary-table { width: 100%; margin-bottom: 25px; border-collapse: separate; border-spacing: 10px 0; margin-left: -10px; margin-right: -10px; }
        .summary-box { background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 4px; padding: 15px; text-align: center; width: 25%; }
        .summary-label { font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; margin-bottom: 5px; display: block; }
        .summary-value { font-size: 18px; font-weight: bold; color: #0f172a; }

        /* Data Tables */
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .data-table th, .data-table td { padding: 10px 12px; border-bottom: 1px solid #e2e8f0; text-align: left; }
        .data-table th { background-color: #f1f5f9; color: #334155; font-size: 10px; text-transform: uppercase; font-weight: bold; }
        .data-table tbody tr:nth-child(even) { background-color: #f8fafc; }
        .data-table tbody tr { page-break-inside: avoid; }

        /* Two Column Layout */
        .col-container { width: 100%; margin-bottom: 20px; }
        .col-half { width: 48%; display: inline-block; vertical-align: top; }
        .col-spacer { width: 2%; display: inline-block; }

        /* Footer */
        .footer { position: fixed; bottom: -20px; left: 0px; right: 0px; height: 30px; font-size: 9px; color: #94a3b8; text-align: center; border-top: 1px solid #e2e8f0; padding-top: 10px; }
        .page-number:after { content: counter(page); }
        
        .currency { white-space: nowrap; }
    </style>
</head>
<body>

    <!-- Header -->
    <table class="header-table">
        <tr>
            <td class="header-logo">
                LicenseHub.
            </td>
            <td class="header-title">
                <h1>Annual Financial Report</h1>
                <span class="text-muted">Periode: 1 Januari - 31 Desember {{ $year }}</span>
            </td>
        </tr>
    </table>

    <!-- Executive Summary -->
    <h2>Executive Summary</h2>
    <table class="summary-table">
        <tr>
            <td class="summary-box">
                <span class="summary-label">Total Annual Spend</span>
                <span class="summary-value currency">Rp {{ number_format($totalAnnualSpend, 0, ',', '.') }}</span>
            </td>
            <td class="summary-box">
                <span class="summary-label">Total Transactions</span>
                <span class="summary-value">{{ number_format($totalPayments) }}</span>
            </td>
            <td class="summary-box">
                <span class="summary-label">Active Vendors</span>
                <span class="summary-value">{{ number_format($totalVendors) }}</span>
            </td>
            <td class="summary-box">
                <span class="summary-label">Active Licenses</span>
                <span class="summary-value">{{ number_format($activeLicenses) }}</span>
            </td>
        </tr>
    </table>

    <!-- Financial Breakdown (2 Columns) -->
    <div class="col-container">
        <!-- Monthly Spending -->
        <div class="col-half">
            <h2>Monthly Spending</h2>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Month</th>
                        <th class="text-right">Spend (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                    @endphp
                    @foreach($monthlySpend as $index => $spend)
                        @if($spend > 0)
                        <tr>
                            <td>{{ $months[$index] }}</td>
                            <td class="text-right currency">{{ number_format($spend, 0, ',', '.') }}</td>
                        </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="col-spacer"></div>

        <!-- Category Spending -->
        <div class="col-half">
            <h2>Spending by Category</h2>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Category</th>
                        <th class="text-right">Spend (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categoryLabels as $index => $label)
                        <tr>
                            <td>{{ $label }}</td>
                            <td class="text-right currency">{{ number_format($categoryData[$index], 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Top Vendors -->
    <h2>Top 5 Vendors by Spend</h2>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%">#</th>
                <th style="width: 40%">Vendor Name</th>
                <th class="text-center" style="width: 15%">Licenses</th>
                <th class="text-right" style="width: 25%">Total Spend (Rp)</th>
                <th class="text-right" style="width: 15%">% of Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($topVendors as $vendor)
            <tr>
                <td>{{ $vendor['rank'] }}</td>
                <td><strong>{{ $vendor['name'] }}</strong></td>
                <td class="text-center">{{ $vendor['licenses'] }}</td>
                <td class="text-right currency">{{ number_format($vendor['spend'], 0, ',', '.') }}</td>
                <td class="text-right">{{ $vendor['pct'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Itemized Detailed Report (May span multiple pages) -->
    <h2 style="page-break-before: always;">Detailed Itemized Transactions</h2>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 15%">Payment Date</th>
                <th style="width: 25%">License Name</th>
                <th style="width: 20%">Category</th>
                <th style="width: 20%">Vendor</th>
                <th class="text-right" style="width: 20%">Cost (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($itemizedPayments as $payment)
            <tr>
                <td>{{ $payment->payment_date->format('d M Y') }}</td>
                <td>{{ $payment->license->name ?? '-' }}</td>
                <td>{{ $payment->license->category->name ?? '-' }}</td>
                <td>{{ $payment->license->vendor->name ?? '-' }}</td>
                <td class="text-right currency">{{ number_format($payment->amount, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center text-muted">No transactions found for this period.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        Generated by LicenseHub Financial System on {{ now()->format('d M Y H:i') }} | Page <span class="page-number"></span>
    </div>

</body>
</html>
