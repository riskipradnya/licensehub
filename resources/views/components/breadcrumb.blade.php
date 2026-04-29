@props(['items' => []])

<nav class="flex items-center gap-2 text-sm" aria-label="Breadcrumb">
    <a href="/dashboard" class="flex items-center gap-1 transition" style="color: var(--color-text-secondary);"
       onmouseover="this.style.color='var(--color-primary)'" onmouseout="this.style.color='var(--color-text-secondary)'">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
        </svg>
    </a>

    @foreach($items as $item)
        <svg class="w-3.5 h-3.5" style="color: var(--color-text-secondary);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>

        @if($loop->last)
            <span class="font-medium" style="color: var(--color-text-primary);">{{ $item['label'] }}</span>
        @else
            <a href="{{ $item['url'] ?? '#' }}" class="transition"
               style="color: var(--color-text-secondary);"
               onmouseover="this.style.color='var(--color-primary)'"
               onmouseout="this.style.color='var(--color-text-secondary)'">
                {{ $item['label'] }}
            </a>
        @endif
    @endforeach
</nav>
