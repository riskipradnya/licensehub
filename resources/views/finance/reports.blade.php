<x-app-layout title="Financial Reports" :breadcrumbs="[['label' => 'Finance', 'url' => '#'], ['label' => 'Reports']]">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold" style="color: var(--color-text-primary);">Financial Reports</h2>
            <p class="text-sm mt-1" style="color: var(--color-text-secondary);">Laporan keuangan dan analisis biaya lisensi</p>
        </div>
        <div class="flex gap-2">
            <select class="form-input w-auto" id="report-year">
                <option>2026</option><option>2025</option><option>2024</option>
            </select>
            <button class="btn btn-secondary text-sm" id="export-report">📊 Export PDF</button>
        </div>
    </div>

    {{-- YEARLY SUMMARY --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
        <x-stat-card label="Total Annual Spend" :value="385000000" variant="info" prefix="Rp " />
        <x-stat-card label="Total Payments" :value="42" variant="active" />
        <x-stat-card label="Total Vendors" :value="12" variant="info" />
        <x-stat-card label="Active Licenses" :value="124" variant="active" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        {{-- MONTHLY SPEND CHART --}}
        <div class="card">
            <h3 class="text-base font-semibold mb-4" style="color: var(--color-text-primary);">Monthly Spending (2026)</h3>
            <canvas id="monthlySpendChart" height="260"></canvas>
        </div>

        {{-- CATEGORY PIE CHART --}}
        <div class="card">
            <h3 class="text-base font-semibold mb-4" style="color: var(--color-text-primary);">Spending by Category</h3>
            <canvas id="categoryChart" height="260"></canvas>
        </div>
    </div>

    {{-- TOP VENDORS TABLE --}}
    <div class="card p-0 overflow-hidden mb-6">
        <div class="px-6 py-4" style="border-bottom: 1px solid var(--color-border);">
            <h3 class="text-base font-semibold" style="color: var(--color-text-primary);">Top Vendors by Spend</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="data-table" id="top-vendors-table">
                <thead><tr><th>#</th><th>Vendor</th><th>Licenses</th><th>Total Spend</th><th>% of Total</th><th>Trend</th></tr></thead>
                <tbody>
                    @php
                    $topVendors = [
                        ['rank' => 1, 'name' => 'Oracle Corp', 'licenses' => 8, 'spend' => 98000000, 'pct' => '25.5%', 'trend' => 'up'],
                        ['rank' => 2, 'name' => 'Microsoft', 'licenses' => 24, 'spend' => 86000000, 'pct' => '22.3%', 'trend' => 'up'],
                        ['rank' => 3, 'name' => 'VMware', 'licenses' => 6, 'spend' => 64000000, 'pct' => '16.6%', 'trend' => 'down'],
                        ['rank' => 4, 'name' => 'Fortinet', 'licenses' => 4, 'spend' => 48000000, 'pct' => '12.5%', 'trend' => 'up'],
                        ['rank' => 5, 'name' => 'Adobe Inc.', 'licenses' => 12, 'spend' => 36000000, 'pct' => '9.4%', 'trend' => 'down'],
                    ];
                    @endphp
                    @foreach($topVendors as $tv)
                    <tr>
                        <td class="font-bold" style="color: var(--color-primary);">{{ $tv['rank'] }}</td>
                        <td class="font-medium">{{ $tv['name'] }}</td>
                        <td>{{ $tv['licenses'] }}</td>
                        <td class="font-semibold">Rp {{ number_format($tv['spend'], 0, ',', '.') }}</td>
                        <td>
                            <div class="flex items-center gap-2">
                                <div class="w-16 h-2 rounded-full overflow-hidden" style="background: var(--color-border);">
                                    <div class="h-full rounded-full" style="width: {{ $tv['pct'] }}; background: var(--color-primary);"></div>
                                </div>
                                <span class="text-xs">{{ $tv['pct'] }}</span>
                            </div>
                        </td>
                        <td>
                            @if($tv['trend'] === 'up')
                                <span class="text-xs font-medium" style="color: var(--color-status-danger);">↑ Naik</span>
                            @else
                                <span class="text-xs font-medium" style="color: var(--color-status-active);">↓ Turun</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@push('scripts')
<script type="module">
    import Chart from 'chart.js/auto';

    // Monthly Spend
    const mc = document.getElementById('monthlySpendChart');
    if (mc) {
        new Chart(mc, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Biaya (Juta Rp)',
                    data: [32, 28, 56, 45, 38, 22, 30, 0, 0, 0, 0, 0],
                    backgroundColor: '#6366f1',
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false } },
                    y: { grid: { color: 'rgba(0,0,0,0.05)' }, ticks: { callback: v => 'Rp ' + v + 'M' } }
                }
            }
        });
    }

    // Category Pie
    const cc = document.getElementById('categoryChart');
    if (cc) {
        new Chart(cc, {
            type: 'doughnut',
            data: {
                labels: ['Software', 'Security', 'OS', 'Antivirus', 'Infrastructure'],
                datasets: [{
                    data: [42, 23, 15, 12, 8],
                    backgroundColor: ['#6366f1', '#f59e0b', '#22c55e', '#ef4444', '#8b5cf6'],
                    borderWidth: 0,
                    hoverOffset: 10,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: { position: 'bottom', labels: { usePointStyle: true, padding: 16, font: { family: 'Inter', size: 12 } } }
                }
            }
        });
    }
</script>
@endpush

</x-app-layout>
