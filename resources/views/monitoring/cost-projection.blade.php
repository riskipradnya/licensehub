<x-app-layout title="Cost Projection" :breadcrumbs="[['label' => 'Monitoring', 'url' => '#'], ['label' => 'Cost Projection']]">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold" style="color: var(--color-text-primary);">Cost Projection</h2>
            <p class="text-sm mt-1" style="color: var(--color-text-secondary);">Perkiraan biaya lisensi yang harus dikeluarkan mendatang</p>
        </div>
        <select class="form-input w-auto" id="projection-range" x-data x-on:change="location.reload()">
            <option value="3">3 Bulan ke Depan</option>
            <option value="6" selected>6 Bulan ke Depan</option>
            <option value="12">12 Bulan ke Depan</option>
        </select>
    </div>

    {{-- SUMMARY CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <x-stat-card label="Total Projected Cost" :value="186500000" variant="info" prefix="Rp " />
        <x-stat-card label="Licenses Due" :value="18" variant="warning" />
        <x-stat-card label="Avg Monthly Cost" :value="31083000" variant="active" prefix="Rp " />
    </div>

    {{-- BAR CHART --}}
    <div class="card mb-6">
        <h3 class="text-base font-semibold mb-4" style="color: var(--color-text-primary);">Projected Cost by Month</h3>
        <canvas id="projectionChart" height="300"></canvas>
    </div>

    {{-- BREAKDOWN TABLE --}}
    <div class="card p-0 overflow-hidden">
        <div class="px-6 py-4" style="border-bottom: 1px solid var(--color-border);">
            <h3 class="text-base font-semibold" style="color: var(--color-text-primary);">Cost Breakdown by License</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="data-table" id="projection-table">
                <thead><tr><th>License</th><th>Vendor</th><th>Due Date</th><th>Days Left</th><th>Estimated Cost</th><th>Status</th></tr></thead>
                <tbody>
                    @php
                    $projections = [
                        ['name' => 'Kaspersky Endpoint', 'vendor' => 'Kaspersky', 'due' => 'Apr 2026', 'days' => 1, 'cost' => 8500000, 'status' => 'expired'],
                        ['name' => 'Oracle Database', 'vendor' => 'Oracle', 'due' => 'Apr 2026', 'days' => 7, 'cost' => 45000000, 'status' => 'expiring'],
                        ['name' => 'Microsoft 365', 'vendor' => 'Microsoft', 'due' => 'Apr 2026', 'days' => 14, 'cost' => 12000000, 'status' => 'expiring'],
                        ['name' => 'Windows Server', 'vendor' => 'Microsoft', 'due' => 'May 2026', 'days' => 21, 'cost' => 25000000, 'status' => 'expiring'],
                        ['name' => 'ESET NOD32', 'vendor' => 'VHP', 'due' => 'May 2026', 'days' => 26, 'cost' => 5500000, 'status' => 'expiring'],
                        ['name' => 'Slack Enterprise', 'vendor' => 'Salesforce', 'due' => 'Jun 2026', 'days' => 60, 'cost' => 18000000, 'status' => 'active'],
                        ['name' => 'Red Hat Linux', 'vendor' => 'Red Hat', 'due' => 'Jul 2026', 'days' => 97, 'cost' => 15000000, 'status' => 'active'],
                        ['name' => 'Fortinet FortiGate', 'vendor' => 'Fortinet', 'due' => 'Aug 2026', 'days' => 128, 'cost' => 32000000, 'status' => 'active'],
                        ['name' => 'Zoom Business', 'vendor' => 'Zoom', 'due' => 'Sep 2026', 'days' => 154, 'cost' => 9500000, 'status' => 'active'],
                        ['name' => 'Jira Software', 'vendor' => 'Atlassian', 'due' => 'Sep 2026', 'days' => 168, 'cost' => 16000000, 'status' => 'active'],
                    ];
                    @endphp
                    @foreach($projections as $p)
                    <tr>
                        <td class="font-medium">{{ $p['name'] }}</td>
                        <td>{{ $p['vendor'] }}</td>
                        <td>{{ $p['due'] }}</td>
                        <td>
                            @if($p['days'] <= 7)
                                <span class="font-semibold" style="color: var(--color-status-danger);">{{ $p['days'] }} hari</span>
                            @elseif($p['days'] <= 30)
                                <span class="font-semibold" style="color: var(--color-status-warning);">{{ $p['days'] }} hari</span>
                            @else
                                <span>{{ $p['days'] }} hari</span>
                            @endif
                        </td>
                        <td class="font-semibold">Rp {{ number_format($p['cost'], 0, ',', '.') }}</td>
                        <td><x-status-badge :status="$p['status']" size="sm" /></td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr><td colspan="4" class="font-bold text-right" style="color: var(--color-text-primary);">Total Projected</td>
                        <td class="font-bold" style="color: var(--color-primary);">Rp 186.500.000</td><td></td></tr>
                </tfoot>
            </table>
        </div>
    </div>

@push('scripts')
<script type="module">
    import Chart from 'chart.js/auto';
    const ctx = document.getElementById('projectionChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Apr 2026', 'May 2026', 'Jun 2026', 'Jul 2026', 'Aug 2026', 'Sep 2026'],
                datasets: [
                    { label: 'Software', data: [57, 30.5, 18, 0, 0, 25.5], backgroundColor: '#6366f1', borderRadius: 6 },
                    { label: 'Security', data: [8.5, 0, 0, 0, 32, 0], backgroundColor: '#f59e0b', borderRadius: 6 },
                    { label: 'OS', data: [0, 0, 0, 15, 0, 0], backgroundColor: '#22c55e', borderRadius: 6 },
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, padding: 16, font: { family: 'Inter', size: 12 } } } },
                scales: {
                    x: { stacked: true, grid: { display: false }, ticks: { font: { family: 'Inter', size: 11 } } },
                    y: { stacked: true, grid: { color: 'rgba(0,0,0,0.05)' }, ticks: { font: { family: 'Inter', size: 11 }, callback: v => 'Rp ' + v + 'M' } }
                }
            }
        });
    }
</script>
@endpush

</x-app-layout>
