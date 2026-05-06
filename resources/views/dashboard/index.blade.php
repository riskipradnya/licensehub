<x-app-layout title="Dashboard" :breadcrumbs="[['label' => 'Dashboard']]">

    {{-- STAT CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6 stat-cards-grid">
        <x-stat-card label="Active Licenses" :value="$activeLicenses" variant="active" />
        <x-stat-card label="Expiring Soon" :value="$expiringSoon" variant="warning" />
        <x-stat-card label="Expired" :value="$expiredLicenses" variant="danger" />
        <x-stat-card label="Total License Cost" :value="$totalMonthlyCost" variant="info" prefix="Rp " />
    </div>

    {{-- CHARTS & ALERTS ROW --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        {{-- LINE CHART (2/3 width) --}}
        <div class="lg:col-span-2 card">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-semibold" style="color: var(--color-text-primary);">License Cost Trend</h3>
                <span class="text-xs px-3 py-1.5 rounded-md" style="background: var(--color-content-bg); color: var(--color-text-secondary);">6 Bulan Terakhir</span>
            </div>
            <canvas id="costTrendChart" height="260"></canvas>
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
<script type="module">
    import Chart from 'chart.js/auto';

    const ctx = document.getElementById('costTrendChart');
    if (ctx) {
        const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($chartLabels),
                datasets: [{
                    label: 'Biaya Lisensi (Juta Rp)',
                    data: @json($chartData),
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
                            label: (ctx) => 'Rp ' + ctx.parsed.y.toFixed(1) + ' Juta'
                        }
                    }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { family: 'Inter', size: 11 } } },
                    y: {
                        grid: { color: isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)' },
                        ticks: {
                            font: { family: 'Inter', size: 11 },
                            callback: (v) => 'Rp ' + v + 'M'
                        }
                    },
                },
            }
        });
    }
</script>
@endpush

</x-app-layout>
