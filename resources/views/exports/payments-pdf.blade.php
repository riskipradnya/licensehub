<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment History Export</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #dddddd; text-align: left; padding: 8px; }
        th { background-color: #f2f2f2; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
    </style>
</head>
<body>
    <h2>Payment History Export</h2>
    
    <table>
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th>Reference Number</th>
                <th>License Name</th>
                <th>Vendor</th>
                <th>Payment Date</th>
                <th>Payment Method</th>
                <th class="text-right">Amount (Rp)</th>
                <th class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @php $grandTotal = 0; @endphp
            @foreach($payments as $index => $payment)
                @php $grandTotal += $payment->amount; @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $payment->reference_number ?? '-' }}</td>
                    <td>{{ $payment->license->name ?? '-' }}</td>
                    <td>{{ $payment->license->vendor->name ?? '-' }}</td>
                    <td>{{ $payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') : '-' }}</td>
                    <td>{{ $payment->payment_method ? ucwords(str_replace('_', ' ', $payment->payment_method)) : '-' }}</td>
                    <td class="text-right">{{ number_format($payment->amount, 0, ',', '.') }}</td>
                    <td class="text-center">{{ ucfirst($payment->status) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="6" class="text-right font-bold">Grand Total</th>
                <th class="text-right font-bold">{{ number_format($grandTotal, 0, ',', '.') }}</th>
                <th></th>
            </tr>
        </tfoot>
    </table>
</body>
</html>
