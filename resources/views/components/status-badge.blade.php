@props([
    'status' => 'active',
    'label' => null,
    'size' => 'md',
])

@php
$statusConfig = [
    'active'   => ['class' => 'badge--active',  'dot' => 'badge-dot--active',  'icon' => '✓', 'text' => 'Active'],
    'expiring' => ['class' => 'badge--warning', 'dot' => 'badge-dot--warning', 'icon' => '⚠', 'text' => 'Expiring'],
    'expired'  => ['class' => 'badge--danger',  'dot' => 'badge-dot--danger',  'icon' => '✕', 'text' => 'Expired'],
    'pending'  => ['class' => 'badge--info',    'dot' => 'badge-dot--info',    'icon' => '◷', 'text' => 'Pending'],
    'inactive' => ['class' => 'badge--neutral', 'dot' => 'badge-dot--neutral', 'icon' => '—', 'text' => 'Inactive'],
    'renewed'  => ['class' => 'badge--active',  'dot' => 'badge-dot--active',  'icon' => '✓', 'text' => 'Renewed'],
    'failed'   => ['class' => 'badge--danger',  'dot' => 'badge-dot--danger',  'icon' => '✕', 'text' => 'Failed'],
    'paid'     => ['class' => 'badge--active',  'dot' => 'badge-dot--active',  'icon' => '✓', 'text' => 'Paid'],
];
$config = $statusConfig[$status] ?? $statusConfig['inactive'];
$displayText = $label ?? $config['text'];
$sizeClass = $size === 'sm' ? 'text-[10px] px-2 py-0.5' : '';
@endphp

<span class="badge {{ $config['class'] }} {{ $sizeClass }}" {{ $attributes }}>
    <span class="badge-dot {{ $config['dot'] }}"></span>
    <span>{{ $displayText }}</span>
</span>
