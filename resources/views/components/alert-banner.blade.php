@props([
    'type' => 'info',
    'message' => '',
    'dismissible' => true,
])

@php
$styles = [
    'info'    => ['bg' => 'var(--color-status-info-bg)', 'border' => 'var(--color-status-info)', 'text' => '#1e40af'],
    'success' => ['bg' => 'var(--color-status-active-bg)', 'border' => 'var(--color-status-active)', 'text' => '#15803d'],
    'warning' => ['bg' => 'var(--color-status-warning-bg)', 'border' => 'var(--color-status-warning)', 'text' => '#b45309'],
    'error'   => ['bg' => 'var(--color-status-danger-bg)', 'border' => 'var(--color-status-danger)', 'text' => '#dc2626'],
];
$s = $styles[$type] ?? $styles['info'];
@endphp

<div x-data="{ visible: true }" x-show="visible" x-transition class="rounded-xl px-4 py-3 flex items-start gap-3 animate-slide-down"
     style="background: {{ $s['bg'] }}; border-left: 4px solid {{ $s['border'] }}; color: {{ $s['text'] }};">
    <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        @if($type === 'success')
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        @elseif($type === 'warning')
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
        @elseif($type === 'error')
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        @else
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        @endif
    </svg>
    <p class="text-sm font-medium flex-1">{{ $message }}{{ $slot }}</p>
    @if($dismissible)
    <button @click="visible = false" class="shrink-0 opacity-60 hover:opacity-100 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </button>
    @endif
</div>
