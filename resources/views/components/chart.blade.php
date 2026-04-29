@props([
    'type' => 'line',
    'id' => 'chart-' . uniqid(),
    'height' => '300',
])

<div class="card">
    @if(isset($title))
    <h3 class="text-base font-semibold mb-4" style="color: var(--color-text-primary);">{{ $title }}</h3>
    @endif
    <canvas id="{{ $id }}" height="{{ $height }}"></canvas>
</div>

@push('scripts')
<script type="module">
    import Chart from 'chart.js/auto';

    const ctx = document.getElementById('{{ $id }}');
    if (ctx) {
        new Chart(ctx, {
            type: '{{ $type }}',
            data: {!! $slot !!},
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { usePointStyle: true, padding: 16, font: { family: 'Inter', size: 12 } } },
                },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { family: 'Inter', size: 11 } } },
                    y: { grid: { color: 'rgba(0,0,0,0.05)' }, ticks: { font: { family: 'Inter', size: 11 } } },
                },
                interaction: { intersect: false, mode: 'index' },
            }
        });
    }
</script>
@endpush
