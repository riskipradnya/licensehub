<x-app-layout title="Dashboard" :breadcrumbs="[['label' => 'Dashboard']]">

    {{-- STAT CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-4 mb-6 stat-cards-grid">
        <x-stat-card label="Active Licenses" :value="$activeLicenses" variant="active" />
        <x-stat-card label="Expiring Soon" :value="$expiringSoon" variant="warning" />
        <x-stat-card label="Expired" :value="$expiredLicenses" variant="danger" />
        <x-stat-card label="Total Investasi Beli Putus" :value="$totalPerpetualCost" variant="info" prefix="Rp " />
        <x-stat-card label="Estimasi Beban Tahunan (ARC)" :value="$annualRecurringCost" variant="warning" prefix="Rp " />
    </div>

    {{-- CHARTS & ALERTS ROW --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        {{-- LINE CHART (2/3 width) --}}
        <div class="lg:col-span-2 card">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-semibold" style="color: var(--color-text-primary);">Tren Biaya Lisensi</h3>
                <form x-data="{ filterType: '{{ $filter }}' }" method="GET" action="{{ route('dashboard') }}" class="flex flex-col sm:flex-row items-center gap-2">
                    <select name="filter" x-model="filterType" @change="if(filterType !== 'custom') $el.form.submit()" class="form-select text-xs py-1.5 pl-3 pr-8 rounded-md border-gray-300 dark:border-slate-600 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" style="background: var(--color-content-bg); color: var(--color-text-primary);">
                        <option value="3_months">Proyeksi 3 Bulan Ke Depan</option>
                        <option value="6_months">Proyeksi 6 Bulan Ke Depan</option>
                        <option value="12_months">Proyeksi 1 Tahun Ke Depan</option>
                        <option value="custom">Custom Range</option>
                    </select>

                    <div x-show="filterType === 'custom'" x-cloak class="flex items-center gap-2">
                        <input type="date" name="start_date" value="{{ request('start_date', $startDateStr ?? '') }}" class="text-xs py-1.5 px-2 rounded-md border-gray-300 dark:border-slate-600 focus:ring-indigo-500 focus:border-indigo-500" style="background: var(--color-content-bg); color: var(--color-text-primary);">
                        <span class="text-xs text-gray-500 dark:text-slate-400">to</span>
                        <input type="date" name="end_date" value="{{ request('end_date', $endDateStr ?? '') }}" class="text-xs py-1.5 px-2 rounded-md border-gray-300 dark:border-slate-600 focus:ring-indigo-500 focus:border-indigo-500" style="background: var(--color-content-bg); color: var(--color-text-primary);">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold py-1.5 px-3 rounded-md shadow-sm transition-colors">
                            Filter
                        </button>
                    </div>
                </form>
            </div>
            <div class="relative w-full h-80 bg-white dark:bg-[#1E293B] border border-slate-200 dark:border-slate-700/50 rounded-xl p-4 shadow-sm">
                <canvas id="trendChart"></canvas>
            </div>
        </div>

        {{-- ALERT FEED (1/3 width) --}}
        <div class="card">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-semibold" style="color: var(--color-text-primary);">Recent Alerts</h3>
                <a href="{{ route('notifications.index') }}" class="text-xs font-medium" style="color: var(--color-primary);">View All →</a>
            </div>
            <div class="space-y-3" id="alert-feed">
                @forelse($alerts as $alert)
                <div class="flex items-start gap-3 p-3 rounded-lg" style="background: var(--color-status-{{ $alert['variant'] }}-bg);">
                    <span class="badge-dot badge-dot--{{ $alert['variant'] }} mt-1.5 shrink-0"></span>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium" style="color: var(--color-text-primary);">{{ $alert['name'] }}</p>
                        <p class="text-xs mt-0.5" style="color: var(--color-status-{{ $alert['variant'] }});">{{ $alert['message'] }}</p>
                    </div>
                    <span class="text-[10px] shrink-0" style="color: var(--color-text-secondary);">{{ $alert['time'] }}</span>
                </div>
                @empty
                <p class="text-sm text-center py-4" style="color: var(--color-text-secondary);">Tidak ada alert saat ini.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- EXPIRING SOON TABLE --}}
    <div class="card p-0 overflow-hidden">
        <div class="px-6 py-4 flex items-center justify-between" style="border-bottom: 1px solid var(--color-border);">
            <h3 class="text-base font-semibold" style="color: var(--color-text-primary);">Licenses Expiring Soon</h3>
            <a href="{{ route('licenses.index', ['status' => 'expiring']) }}" class="btn btn-ghost text-xs">View All Licenses →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="data-table" id="expiring-licenses-table">
                <thead>
                    <tr>
                        <th>License Name</th>
                        <th>Vendor</th>
                        <th>Category</th>
                        <th>Expiry Date</th>
                        <th>Days Left</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expiringLicenses as $license)
                    <tr>
                        <td class="font-medium">{{ $license->name }}</td>
                        <td>{{ $license->vendor->name ?? '—' }}</td>
                        <td>
                            <span class="text-xs px-2 py-0.5 rounded-md"
                                  style="background: var(--color-status-{{ $license->category_color }}-bg); color: var(--color-status-{{ $license->category_color }});">
                                {{ $license->category->name ?? '—' }}
                            </span>
                        </td>
                        <td>{{ $license->expiry_date->format('d M Y') }}</td>
                        <td>
                            @if($license->days_until_expiry <= 7)
                                <span class="font-semibold" style="color: var(--color-status-danger);">{{ $license->days_until_expiry }} hari</span>
                            @else
                                <span class="font-semibold" style="color: var(--color-status-warning);">{{ $license->days_until_expiry }} hari</span>
                            @endif
                        </td>
                        <td>
                            <x-status-badge :status="$license->days_until_expiry <= 7 ? 'expired' : 'expiring'" :label="'H-'.$license->days_until_expiry" />
                        </td>
                        <td>
                            <a href="{{ route('licenses.show', $license) }}" class="btn btn-primary text-xs py-1.5 px-3">Detail →</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-6">
                            <p class="text-sm" style="color: var(--color-text-secondary);">🎉 Tidak ada lisensi yang akan kedaluwarsa dalam 60 hari ke depan.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('trendChart');
    if (ctx) {
        let isDark = document.documentElement.getAttribute('data-theme') === 'dark';
        
        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($chartLabels),
                datasets: [{
                    label: 'Biaya Lisensi (Rp)',
                    data: @json($chartValues),
                    borderColor: '#6366f1',
                    backgroundColor: 'rgba(99, 102, 241, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#6366f1',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => 'Rp ' + new Intl.NumberFormat('id-ID').format(ctx.parsed.y)
                        }
                    }
                },
                scales: {
                    x: { 
                        grid: { display: false }, 
                        ticks: { 
                            font: { family: 'Inter', size: 11 },
                            color: isDark ? '#94a3b8' : '#64748b'
                        } 
                    },
                    y: {
                        grid: { color: isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)' },
                        ticks: {
                            color: isDark ? '#94a3b8' : '#64748b',
                            font: { family: 'Inter', size: 11 },
                            callback: (v) => {
                                if (v >= 1000000) return 'Rp ' + (v / 1000000) + ' Juta';
                                if (v >= 1000) return 'Rp ' + (v / 1000) + ' Ribu';
                                return 'Rp ' + v;
                            }
                        }
                    },
                },
            }
        });

        // MutationObserver to sync Chart.js colors with Tailwind Dark Mode toggle
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.attributeName === 'data-theme' || mutation.attributeName === 'class') {
                    isDark = document.documentElement.getAttribute('data-theme') === 'dark' || document.documentElement.classList.contains('dark');
                    
                    // Update Chart colors
                    chart.options.scales.x.ticks.color = isDark ? '#94a3b8' : '#64748b';
                    chart.options.scales.y.ticks.color = isDark ? '#94a3b8' : '#64748b';
                    chart.options.scales.y.grid.color = isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)';
                    
                    chart.update();
                }
            });
        });

        observer.observe(document.documentElement, { attributes: true });
    }
});
</script>
@endpush

</x-app-layout>
