<x-app-layout title="Payment History" :breadcrumbs="[['label' => 'Finance', 'url' => '#'], ['label' => 'Payment History']]">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold" style="color: var(--color-text-primary);">Payment History</h2>
            <p class="text-sm mt-1" style="color: var(--color-text-secondary);">Riwayat seluruh pembayaran lisensi</p>
        </div>
        <button class="btn btn-secondary text-sm" id="export-payments">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            Export CSV
        </button>
    </div>

    {{-- FILTERS --}}
    <div class="card mb-6">
        <div class="flex flex-col md:flex-row gap-3">
            <input type="date" class="form-input w-full md:w-auto" id="pay-date-from">
            <span class="hidden md:flex items-center text-sm" style="color: var(--color-text-secondary);">to</span>
            <input type="date" class="form-input w-full md:w-auto" id="pay-date-to">
            <select class="form-input w-full md:w-40"><option value="">All Status</option><option>Paid</option><option>Pending</option><option>Failed</option></select>
            <select class="form-input w-full md:w-40"><option value="">All Methods</option><option>Bank Transfer</option><option>Midtrans</option></select>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="card p-0 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table" id="payment-history-table">
                <thead><tr><th>Invoice #</th><th>License</th><th>Amount</th><th>Method</th><th>Date</th><th>Paid By</th><th>Status</th><th>Receipt</th></tr></thead>
                <tbody>
                    @php
                    $payments = [
                        ['inv' => 'INV-2026-048', 'name' => 'Adobe Creative Cloud', 'amount' => 18000000, 'method' => 'Bank Transfer', 'date' => '11 Apr 2026', 'by' => 'Finance Mgr', 'status' => 'paid'],
                        ['inv' => 'INV-2026-047', 'name' => 'VMware vSphere', 'amount' => 32000000, 'method' => 'Midtrans', 'date' => '08 Apr 2026', 'by' => 'Finance Mgr', 'status' => 'paid'],
                        ['inv' => 'INV-2026-046', 'name' => 'Zoom Business', 'amount' => 9500000, 'method' => 'Bank Transfer', 'date' => '01 Apr 2026', 'by' => 'Finance Staff', 'status' => 'paid'],
                        ['inv' => 'INV-2026-045', 'name' => 'Jira Software', 'amount' => 16000000, 'method' => 'Midtrans', 'date' => '28 Mar 2026', 'by' => 'Finance Mgr', 'status' => 'paid'],
                        ['inv' => 'INV-2026-044', 'name' => 'Fortinet FortiGate', 'amount' => 32000000, 'method' => 'Bank Transfer', 'date' => '15 Mar 2026', 'by' => 'Finance Staff', 'status' => 'failed'],
                        ['inv' => 'INV-2026-043', 'name' => 'Red Hat Linux', 'amount' => 15000000, 'method' => 'Bank Transfer', 'date' => '01 Mar 2026', 'by' => 'Finance Mgr', 'status' => 'paid'],
                    ];
                    @endphp
                    @foreach($payments as $pay)
                    <tr>
                        <td class="font-mono text-xs font-medium" style="color: var(--color-primary);">{{ $pay['inv'] }}</td>
                        <td class="font-medium">{{ $pay['name'] }}</td>
                        <td class="font-semibold">Rp {{ number_format($pay['amount'], 0, ',', '.') }}</td>
                        <td><span class="text-xs">{{ $pay['method'] }}</span></td>
                        <td>{{ $pay['date'] }}</td>
                        <td>{{ $pay['by'] }}</td>
                        <td><x-status-badge :status="$pay['status']" size="sm" /></td>
                        <td>
                            @if($pay['status'] === 'paid')
                            <button class="btn-ghost p-1.5 rounded-lg" title="Download Receipt">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            </button>
                            @else
                            <span class="text-xs" style="color: var(--color-text-secondary);">—</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-3 flex items-center justify-between" style="border-top: 1px solid var(--color-border);">
            <span class="text-xs" style="color: var(--color-text-secondary);">Showing 1-6 of 48 payments</span>
            <div class="pagination">
                <span class="pagination-item">◀</span>
                <span class="pagination-item active">1</span>
                <span class="pagination-item">2</span>
                <span class="pagination-item">3</span>
                <span class="pagination-item">▶</span>
            </div>
        </div>
    </div>

</x-app-layout>
