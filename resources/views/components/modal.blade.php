@props([
    'id' => 'modal',
    'title' => '',
    'maxWidth' => 'md',
])

@php
$widthClass = match($maxWidth) {
    'sm' => 'max-w-sm',
    'md' => 'max-w-lg',
    'lg' => 'max-w-2xl',
    'xl' => 'max-w-4xl',
    default => 'max-w-lg',
};
@endphp

<div x-data="modal" x-on:open-modal-{{ $id }}.window="show()" x-on:close-modal-{{ $id }}.window="hide()">
    <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0"
         class="modal-backdrop" @click.self="hide()" style="display:none;" id="{{ $id }}">
        <div class="modal-content {{ $widthClass }}" @click.stop
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">
            {{-- HEADER --}}
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold" style="color: var(--color-text-primary);">{{ $title }}</h3>
                <button @click="hide()" class="btn-ghost p-1.5 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            {{-- BODY --}}
            <div>{{ $slot }}</div>
            {{-- FOOTER --}}
            @if(isset($footer))
            <div class="mt-6 flex items-center justify-end gap-3 pt-4" style="border-top: 1px solid var(--color-border);">
                {{ $footer }}
            </div>
            @endif
        </div>
    </div>
</div>
