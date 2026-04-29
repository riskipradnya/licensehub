@props([
    'value' => 0,
    'label' => '',
    'icon' => 'chart-bar',
    'trend' => null,
    'trendValue' => '',
    'variant' => 'info',
    'prefix' => '',
    'suffix' => '',
])

<div class="card stat-card stat-card--{{ $variant }}">
    <div class="flex items-start justify-between">
        <div>
            <p class="text-sm font-medium mb-1" style="color: var(--color-text-secondary);">{{ $label }}</p>
            <p class="text-2xl font-bold tracking-tight" style="color: var(--color-text-primary);"
               x-countup="{{ $value }}" data-prefix="{{ $prefix }}" data-suffix="{{ $suffix }}">
                {{ $prefix }}{{ number_format($value) }}{{ $suffix }}
            </p>
            @if($trend !== null)
            <div class="flex items-center gap-1 mt-2 text-xs font-medium">
                @if($trend === 'up')
                    <svg class="w-3.5 h-3.5" style="color: var(--color-status-active);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                    </svg>
                    <span style="color: var(--color-status-active);">{{ $trendValue }}</span>
                @else
                    <svg class="w-3.5 h-3.5" style="color: var(--color-status-danger);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                    <span style="color: var(--color-status-danger);">{{ $trendValue }}</span>
                @endif
                <span style="color: var(--color-text-secondary);">vs last month</span>
            </div>
            @endif
        </div>
        <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0"
             style="background: var(--color-status-{{ $variant }}-bg, #eff6ff);">
            @switch($variant)
                @case('active')
                    <svg class="w-5 h-5" style="color: var(--color-status-active);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    @break
                @case('warning')
                    <svg class="w-5 h-5" style="color: var(--color-status-warning);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                    @break
                @case('danger')
                    <svg class="w-5 h-5" style="color: var(--color-status-danger);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    @break
                @default
                    <svg class="w-5 h-5" style="color: var(--color-primary);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            @endswitch
        </div>
    </div>
</div>
