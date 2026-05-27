<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Cost Projection Export</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #cbd5e1; padding: 10px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        
        /* Corporate Table Styles */
        .bg-dark { background-color: #1E293B; color: #ffffff; }
        .bg-zebra { background-color: #F8FAFC; }
        .grand-total-row th { background-color: #E2E8F0; border-top: 3px double #475569; border-bottom: 3px double #475569; color: #1E293B; font-size: 13px; }
        .meta-header th { border: none; background: none; color: #333; text-align: left; padding: 3px 0; }
    </style>
</head>
<body>
    <table>
        <thead>
            <tr class="meta-header">
                <th colspan="5" style="font-size: 16px; font-weight: bold; color: #1e293b;">LICENSEHUB - SISTEM MANAJEMEN LISENSI</th>
            </tr>
            <tr class="meta-header">
                <th colspan="5" style="font-size: 14px; font-weight: bold;">Laporan Proyeksi Biaya Lisensi (Cost Projection)</th>
            </tr>
            <tr class="meta-header">
                <th colspan="5">Periode: {{ $startDate->format('d M Y') }} s/d {{ $endDate->format('d M Y') }}</th>
            </tr>
            <tr class="meta-header">
                <th colspan="5">Tanggal Cetak: {{ now()->format('d M Y H:i') }}</th>
            </tr>
            <tr class="meta-header">
                <th colspan="5" style="height: 10px;"></th>
            </tr>
            <tr>
                <th class="text-center bg-dark" style="width: 5%;">No</th>
                <th class="text-left bg-dark" style="width: 30%;">License Name</th>
                <th class="text-left bg-dark" style="width: 25%;">Vendor</th>
                <th class="text-center bg-dark" style="width: 20%;">Target / Expiry Date</th>
                <th class="text-right bg-dark" style="width: 20%;">Projected Cost (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @php $grandTotal = 0; @endphp
            @foreach($licenses as $index => $license)
                @php $grandTotal += $license->cost; @endphp
                <tr class="{{ $index % 2 !== 0 ? 'bg-zebra' : '' }}">
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-left">{{ $license->name ?? '-' }}</td>
                    <td class="text-left">{{ $license->vendor->name ?? '-' }}</td>
                    <td class="text-center">{{ $license->expiry_date ? \Carbon\Carbon::parse($license->expiry_date)->format('d M Y') : '-' }}</td>
                    <td class="text-right">
                        @if(isset($isExcel) && $isExcel)
                            {{ $license->cost }}
                        @else
                            Rp {{ number_format($license->cost, 0, ',', '.') }}
                        @endif
                    </td>
                </tr>
            @endforeach
            @if($licenses->isEmpty())
                <tr>
                    <td colspan="5" class="text-center" style="padding: 20px; color: #94a3b8;">
                        Tidak ada proyeksi lisensi jatuh tempo dalam periode ini.
                    </td>
                </tr>
            @endif
        </tbody>
        <tfoot>
            <tr class="grand-total-row">
                <th colspan="4" class="text-right font-bold">GRAND TOTAL:</th>
                <th class="text-right font-bold">
                    @if(isset($isExcel) && $isExcel)
                        {{ $grandTotal }}
                    @else
                        Rp {{ number_format($grandTotal, 0, ',', '.') }}
                    @endif
                </th>
            </tr>
        </tfoot>
    </table>
</body>
</html>
