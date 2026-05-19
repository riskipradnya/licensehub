<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Receipt - {{ $payment->reference_number ?? 'REC' }}</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #333; margin: 0; padding: 20px; }
        .container { width: 100%; max-width: 800px; margin: 0 auto; border: 1px solid #ddd; padding: 30px; box-sizing: border-box; }
        table { width: 100%; border-collapse: collapse; }
        
        /* Header */
        .header-table { margin-bottom: 30px; border-bottom: 1px solid #eee; padding-bottom: 20px; }
        .header-table td { vertical-align: top; }
        .company-name { font-size: 20px; font-weight: bold; margin: 0 0 5px 0; color: #222; }
        .company-info { font-size: 12px; color: #666; margin: 0 0 3px 0; }
        .receipt-title { font-size: 28px; font-weight: bold; text-transform: uppercase; color: #222; margin: 0 0 10px 0; text-align: right; letter-spacing: 1px; }
        .receipt-meta { font-size: 13px; color: #666; text-align: right; margin: 0 0 4px 0; }
        .receipt-meta strong { color: #222; }
        
        /* Info Section (Split 2 Column via Table) */
        .info-table { margin-bottom: 30px; }
        .info-table td { vertical-align: top; width: 50%; }
        .section-title { font-size: 11px; font-weight: bold; color: #999; text-transform: uppercase; letter-spacing: 1px; margin: 0 0 5px 0; }
        .info-value-title { font-size: 16px; font-weight: bold; color: #222; margin: 0 0 5px 0; }
        .info-value-text { font-size: 13px; color: #555; margin: 0; }
        
        /* Details Grid */
        .details-table td { padding: 3px 0; font-size: 13px; }
        .details-table .label { color: #666; }
        .details-table .value { font-weight: bold; color: #222; text-align: right; }
        .badge-paid { background-color: #d1fae5; color: #065f46; font-size: 11px; font-weight: bold; padding: 3px 8px; border-radius: 3px; display: inline-block; }
        
        /* Main Item Table */
        .items-table { width: 100%; margin-bottom: 30px; border: 1px solid #eee; }
        .items-table th { background-color: #f9fafb; color: #4b5563; font-size: 12px; text-transform: uppercase; padding: 12px 15px; text-align: left; border-bottom: 1px solid #eee; }
        .items-table td { padding: 15px; border-bottom: 1px solid #f3f4f6; font-size: 13px; color: #333; }
        .items-table th.text-center, .items-table td.text-center { text-align: center; }
        .items-table th.text-right, .items-table td.text-right { text-align: right; }
        .item-name { font-weight: bold; margin: 0 0 4px 0; }
        .item-category { font-size: 11px; color: #666; margin: 0; }
        
        /* Summary (Align Right) */
        .summary-wrapper { width: 100%; }
        .summary-table { width: 50%; float: right; margin-bottom: 30px; }
        .summary-table td { padding: 8px 0; font-size: 13px; }
        .summary-table .label { color: #555; }
        .summary-table .value { font-weight: bold; color: #222; text-align: right; }
        .summary-table .border-b { border-bottom: 1px solid #eee; }
        .summary-table .total-row td { padding-top: 12px; font-size: 16px; font-weight: bold; color: #222; }
        .summary-table .total-row .total-value { color: #2563eb; text-align: right; }
        .clear { clear: both; }
        
        /* Footer */
        .footer { border-top: 1px solid #eee; padding-top: 20px; text-align: center; }
        .footer-note { font-size: 13px; color: #666; font-weight: bold; margin: 0 0 5px 0; }
        .footer-audit { font-size: 11px; color: #999; margin: 0; line-height: 1.4; }
    </style>
</head>
<body>

    <div class="container">
        <!-- Header Section -->
        <table class="header-table">
            <tr>
                <td style="width: 60%;">
                    <p class="company-name">PT. Nama Perusahaan Anda</p>
                    <p class="company-info">NPWP: 01.234.567.8-901.000</p>
                    <p class="company-info">Jl. Contoh Alamat No. 123, Bali</p>
                    <p class="company-info">finance@perusahaan.com | +62 812-3456-7890</p>
                </td>
                <td style="width: 40%; text-align: right;">
                    <p class="receipt-title">Receipt</p>
                    <p class="receipt-meta">Receipt No: <strong>{{ $payment->reference_number ?? '—' }}</strong></p>
                    <p class="receipt-meta">Date: <strong>{{ $payment->payment_date ? $payment->payment_date->format('d M Y, H:i') : '—' }}</strong></p>
                </td>
            </tr>
        </table>

        <!-- Info Section -->
        <table class="info-table">
            <tr>
                <td style="padding-right: 20px;">
                    <p class="section-title">Paid To (Vendor)</p>
                    <p class="info-value-title">{{ $payment->license->vendor->name ?? '—' }}</p>
                    <p class="info-value-text">{{ $payment->license->vendor->bank_name ?? '—' }}</p>
                    <p class="info-value-text">Acct: {{ $payment->license->vendor->bank_account_number ?? '—' }}</p>
                </td>
                <td style="padding-left: 20px;">
                    <p class="section-title">Payment Details</p>
                    <table class="details-table">
                        <tr>
                            <td class="label">Paid By:</td>
                            <td class="value">Sistem LicenseHub (via Xendit)</td>
                        </tr>
                        <tr>
                            <td class="label">Payment Method:</td>
                            <td class="value">{{ str_replace('_', ' ', Str::title($payment->payment_method ?? '—')) }}</td>
                        </tr>
                        <tr>
                            <td class="label">Transaction ID:</td>
                            <td class="value">{{ $payment->id }}</td>
                        </tr>
                        <tr>
                            <td class="label">Status:</td>
                            <td class="value">
                                <span class="badge-paid">PAID / LUNAS</span>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- Table Section -->
        <table class="items-table">
            <thead>
                <tr>
                    <th>License Item</th>
                    <th>Valid Period</th>
                    <th class="text-center">Qty</th>
                    <th class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <p class="item-name">{{ $payment->license->name ?? '—' }}</p>
                        <p class="item-category">{{ $payment->license->category->name ?? 'Software License' }}</p>
                    </td>
                    <td>
                        {{ $payment->license->start_date ? $payment->license->start_date->format('d M Y') : '—' }} - 
                        {{ $payment->license->expiry_date ? $payment->license->expiry_date->format('d M Y') : '—' }}
                    </td>
                    <td class="text-center">1</td>
                    <td class="text-right">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Summary Section -->
        <div class="summary-wrapper">
            <table class="summary-table">
                <tr>
                    <td class="label">Subtotal</td>
                    <td class="value">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    @php
                        // Menghitung PPN 12% sesuai regulasi terbaru
                        $tax = $payment->amount * 0.12;
                        $totalPaid = $payment->amount + $tax;
                    @endphp
                    <td class="label border-b">Tax (PPN 12%)</td>
                    <td class="value border-b">Rp {{ number_format($tax, 0, ',', '.') }}</td>
                </tr>
                <tr class="total-row">
                    <td>Total Paid</td>
                    <td class="total-value">Rp {{ number_format($totalPaid, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>
        <div class="clear"></div>

        <!-- Footer Section -->
        <div class="footer">
            <p class="footer-note">Terima kasih atas pembayarannya.</p>
            <p class="footer-audit">Dokumen ini diterbitkan secara otomatis oleh sistem LicenseHub dan sah sebagai bukti pembayaran yang valid tanpa tanda tangan basah.</p>
        </div>
    </div>

</body>
</html>
