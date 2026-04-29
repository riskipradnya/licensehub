<x-app-layout title="Dashboard" :breadcrumbs="[['label' => 'Dashboard']]">

    {{-- STAT CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6 stat-cards-grid">
        <x-stat-card label="Active Licenses" :value="124" variant="active" trend="up" trendValue="+5" />
        <x-stat-card label="Expiring Soon" :value="18" variant="warning" trend="up" trendValue="+3" />
        <x-stat-card label="Expired" :value="7" variant="danger" trend="down" trendValue="-2" />
        <x-stat-card label="Monthly Cost" :value="24500000" variant="info" prefix="Rp " trend="up" trendValue="+12%" />
    </div>

    {{-- CHARTS & ALERTS ROW --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        {{-- LINE CHART (2/3 width) --}}
        <div class="lg:col-span-2 card">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-semibold" style="color: var(--color-text-primary);">License Cost Trend</h3>
                <select class="text-xs form-input w-auto py-1.5 px-3" id="chart-period-select">
                    <option>6 Bulan Terakhir</option>
                    <option>12 Bulan Terakhir</option>
                </select>
            </div>
            <canvas id="costTrendChart" height="260"></canvas>
        </div>

        {{-- ALERT FEED (1/3 width) --}}
        <div class="card">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-semibold" style="color: var(--color-text-primary);">Recent Alerts</h3>
                <a href="/notifications" class="text-xs font-medium" style="color: var(--color-primary);">View All →</a>
            </div>
            <div class="space-y-3" id="alert-feed">
                {{-- Alert Item 1 --}}
                <div class="flex items-start gap-3 p-3 rounded-lg" style="background: var(--color-status-danger-bg);">
                    <span class="badge-dot badge-dot--danger mt-1.5 shrink-0"></span>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium" style="color: var(--color-text-primary);">Kaspersky Endpoint</p>
                        <p class="text-xs mt-0.5" style="color: var(--color-status-danger);">H-1 — Kedaluwarsa besok</p>
                    </div>
                    <span class="text-[10px] shrink-0" style="color: var(--color-text-secondary);">14:23</span>
                </div>
                {{-- Alert Item 2 --}}
                <div class="flex items-start gap-3 p-3 rounded-lg" style="background: var(--color-status-warning-bg);">
                    <span class="badge-dot badge-dot--warning mt-1.5 shrink-0"></span>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium" style="color: var(--color-text-primary);">Oracle Database</p>
                        <p class="text-xs mt-0.5" style="color: var(--color-status-warning);">H-7 — 21 Apr 2026</p>
                    </div>
                    <span class="text-[10px] shrink-0" style="color: var(--color-text-secondary);">08:00</span>
                </div>
                {{-- Alert Item 3 --}}
                <div class="flex items-start gap-3 p-3 rounded-lg" style="background: var(--color-status-warning-bg);">
                    <span class="badge-dot badge-dot--warning mt-1.5 shrink-0"></span>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium" style="color: var(--color-text-primary);">Microsoft 365</p>
                        <p class="text-xs mt-0.5" style="color: var(--color-status-warning);">H-14 — 28 Apr 2026</p>
                    </div>
                    <span class="text-[10px] shrink-0" style="color: var(--color-text-secondary);">Y'day</span>
                </div>
                {{-- Alert Item 4 --}}
                <div class="flex items-start gap-3 p-3 rounded-lg" style="background: var(--color-status-active-bg);">
                    <span class="badge-dot badge-dot--active mt-1.5 shrink-0"></span>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium" style="color: var(--color-text-primary);">Adobe Creative Cloud</p>
                        <p class="text-xs mt-0.5" style="color: var(--color-status-active);">Renewed successfully</p>
                    </div>
                    <span class="text-[10px] shrink-0" style="color: var(--color-text-secondary);">2d ago</span>
                </div>
            </div>
        </div>
    </div>

    {{-- EXPIRING SOON TABLE --}}
    <div class="card p-0 overflow-hidden">
        <div class="px-6 py-4 flex items-center justify-between" style="border-bottom: 1px solid var(--color-border);">
            <h3 class="text-base font-semibold" style="color: var(--color-text-primary);">Licenses Expiring Soon</h3>
            <a href="/licenses" class="btn btn-ghost text-xs">View All Licenses →</a>
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
                    <tr>
                        <td class="font-medium">Kaspersky Endpoint Security</td>
                        <td>Kaspersky Lab</td>
                        <td><span class="text-xs px-2 py-0.5 rounded-md" style="background: var(--color-primary-50); color: var(--color-primary);">Security</span></td>
                        <td>15 Apr 2026</td>
                        <td><span class="font-semibold" style="color: var(--color-status-danger);">1 hari</span></td>
                        <td><x-status-badge status="expired" label="H-1" /></td>
                        <td><a href="/payments/process/1" class="btn btn-primary text-xs py-1.5 px-3">Bayar →</a></td>
                    </tr>
                    <tr>
                        <td class="font-medium">Oracle Database Enterprise</td>
                        <td>Oracle Corp</td>
                        <td><span class="text-xs px-2 py-0.5 rounded-md" style="background: var(--color-status-info-bg); color: var(--color-status-info);">Software</span></td>
                        <td>21 Apr 2026</td>
                        <td><span class="font-semibold" style="color: var(--color-status-warning);">7 hari</span></td>
                        <td><x-status-badge status="expiring" label="H-7" /></td>
                        <td><a href="/payments/process/2" class="btn btn-primary text-xs py-1.5 px-3">Bayar →</a></td>
                    </tr>
                    <tr>
                        <td class="font-medium">Microsoft 365 Business</td>
                        <td>Microsoft</td>
                        <td><span class="text-xs px-2 py-0.5 rounded-md" style="background: var(--color-status-info-bg); color: var(--color-status-info);">Software</span></td>
                        <td>28 Apr 2026</td>
                        <td><span class="font-semibold" style="color: var(--color-status-warning);">14 hari</span></td>
                        <td><x-status-badge status="expiring" label="H-14" /></td>
                        <td><a href="/payments/process/3" class="btn btn-primary text-xs py-1.5 px-3">Bayar →</a></td>
                    </tr>
                    <tr>
                        <td class="font-medium">Windows Server 2022</td>
                        <td>Microsoft</td>
                        <td><span class="text-xs px-2 py-0.5 rounded-md" style="background: var(--color-status-active-bg); color: var(--color-status-active);">OS</span></td>
                        <td>05 May 2026</td>
                        <td><span class="font-semibold" style="color: var(--color-status-warning);">21 hari</span></td>
                        <td><x-status-badge status="expiring" label="H-21" /></td>
                        <td><a href="/payments/process/4" class="btn btn-secondary text-xs py-1.5 px-3">Detail</a></td>
                    </tr>
                    <tr>
                        <td class="font-medium">ESET NOD32 Antivirus</td>
                        <td>VHP</td>
                        <td><span class="text-xs px-2 py-0.5 rounded-md" style="background: var(--color-status-danger-bg); color: var(--color-status-danger);">Antivirus</span></td>
                        <td>10 May 2026</td>
                        <td><span class="font-semibold" style="color: var(--color-status-warning);">26 hari</span></td>
                        <td><x-status-badge status="expiring" label="H-26" /></td>
                        <td><a href="/payments/process/5" class="btn btn-secondary text-xs py-1.5 px-3">Detail</a></td>
                    </tr>
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
                labels: ['Nov', 'Dec', 'Jan', 'Feb', 'Mar', 'Apr'],
                datasets: [{
                    label: 'Biaya Lisensi (Juta Rp)',
                    data: [18.5, 22.0, 19.8, 24.5, 21.3, 24.5],
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
