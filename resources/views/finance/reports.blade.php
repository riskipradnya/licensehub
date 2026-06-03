<x-app-layout title="Financial Reports" :breadcrumbs="[['label' => 'Finance', 'url' => '#'], ['label' => 'Reports']]">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold" style="color: var(--color-text-primary);">Financial Reports</h2>
            <p class="text-sm mt-1" style="color: var(--color-text-secondary);">Laporan keuangan dan analisis biaya lisensi</p>
        </div>
        <div class="flex gap-2">
            <select class="form-input w-auto" id="report-year" onchange="window.location.href='?year='+this.value">
                @foreach($availableYears as $y)
                    <option value="{{ $y }}" @selected($y == $year)>{{ $y }}</option>
                @endforeach
            </select>
            <a href="{{ route('reports.exportPdf', ['year' => $year]) }}" class="btn btn-secondary text-sm">📊 Export PDF</a>
        </div>
    </div>

    <div id="report-content">
        {{-- YEARLY SUMMARY --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
        <x-stat-card label="Total Annual Spend" :value="$totalAnnualSpend" variant="info" prefix="Rp " />
        <x-stat-card label="Total Payments" :value="$totalPayments" variant="active" />
        <x-stat-card label="Total Vendors" :value="$totalVendors" variant="info" />
        <x-stat-card label="Active Licenses" :value="$activeLicenses" variant="active" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        {{-- MONTHLY SPEND CHART --}}
        <div class="card">
            <h3 class="text-base font-semibold mb-4" style="color: var(--color-text-primary);">Monthly Spending ({{ $year }})</h3>
            <div class="relative w-full h-[300px]">
                <canvas id="monthlySpendChart"></canvas>
            </div>
        </div>

        {{-- CATEGORY PIE CHART --}}
        <div class="card">
            <h3 class="text-base font-semibold mb-4" style="color: var(--color-text-primary);">Spending by Category</h3>
            <div class="relative w-full h-[300px]">
                <canvas id="categoryChart"></canvas>
            </div>
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
                    @forelse($topVendors as $tv)
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
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-sm" style="color: var(--color-text-secondary);">Belum ada data pembayaran di tahun {{ $year }}.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    </div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const formatRupiah = function(context) {
            let value = context.raw || 0;
            return 'Rp ' + value.toLocaleString('id-ID');
        };

        // Monthly Spend Chart
        const mc = document.getElementById('monthlySpendChart');
        if (mc) {
            new Chart(mc, {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    datasets: [{
                        label: 'Biaya',
                        data: @json($monthlySpend),
                        backgroundColor: '#6366f1',
                        borderRadius: 6,
                    }]
                },
                options: {
                    responsive: true, 
                    maintainAspectRatio: false,
                    plugins: { 
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: formatRupiah
                            }
                        }
                    },
                    scales: {
                        x: { grid: { display: false } },
                        y: { 
                            beginAtZero: true,
                            suggestedMax: 1000000,
                            grid: { color: 'rgba(0,0,0,0.05)' }, 
                            ticks: { 
                                callback: function(value) {
                                    if(value === 0) return 'Rp 0';
                                    return 'Rp ' + (value / 1000000) + 'M';
                                } 
                            } 
                        }
                    }
                }
            });
        }

        // Category Pie Chart
        const cc = document.getElementById('categoryChart');
        if (cc) {
            new Chart(cc, {
                type: 'doughnut',
                data: {
                    labels: @json($categoryLabels),
                    datasets: [{
                        data: @json($categoryData),
                        backgroundColor: ['#6366f1', '#f59e0b', '#22c55e', '#ef4444', '#8b5cf6', '#3b82f6', '#14b8a6', '#f43f5e', '#a855f7', '#06b6d4'],
                        borderWidth: 0,
                        hoverOffset: 10,
                    }]
                },
                options: {
                    responsive: true, 
                    maintainAspectRatio: false,
                    cutout: '65%',
                    plugins: {
                        legend: { 
                            position: 'bottom', 
                            labels: { usePointStyle: true, padding: 16, font: { family: 'Inter', size: 12 } } 
                        },
                        tooltip: {
                            callbacks: {
                                label: formatRupiah
                            }
                        }
                    }
                }
            });
        }
    });

</script>
@endpush

</x-app-layout>
