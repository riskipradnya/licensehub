@props([
    'align' => 'right',
    'width' => '48',
])

<div x-data="dropdown" class="relative inline-block" {{ $attributes }}>
    {{-- TRIGGER --}}
    <div @click="toggle()">
        {{ $trigger }}
    </div>

    {{-- CONTENT --}}
    <div x-show="open" @click.away="close()" x-transition
         class="absolute {{ $align === 'right' ? 'right-0' : 'left-0' }} mt-2 w-{{ $width }} rounded-xl shadow-lg py-1 z-50"
         style="background: var(--color-card-bg); border: 1px solid var(--color-border);"
         x-cloak>
        {{ $slot }}
    </div>
</div>
