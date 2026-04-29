@props(['count' => 0])

<div x-data="dropdown" class="relative notification-bell" id="notification-bell">
    <button @click="toggle()" class="btn-ghost p-2 rounded-lg relative">
        <svg class="w-5 h-5" style="color: var(--color-text-secondary);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
        </svg>
        @if($count > 0)
        <span class="badge-count">{{ $count > 9 ? '9+' : $count }}</span>
        @endif
    </button>

    <div x-show="open" @click.away="close()" x-transition
         class="absolute right-0 mt-2 w-80 rounded-xl shadow-lg z-50 overflow-hidden"
         style="background: var(--color-card-bg); border: 1px solid var(--color-border);"
         x-cloak>
        <div class="px-4 py-3 font-semibold text-sm flex items-center justify-between"
             style="border-bottom: 1px solid var(--color-border); color: var(--color-text-primary);">
            <span>Notifications</span>
            @if($count > 0)
            <span class="badge badge--danger text-[10px]">{{ $count }} new</span>
            @endif
        </div>
        <div class="max-h-80 overflow-y-auto divide-y" style="border-color: var(--color-border);">
            {{ $slot }}
            @if(!isset($slot) || (string)$slot === '')
            <div class="px-4 py-6 text-center text-sm" style="color: var(--color-text-secondary);">
                Tidak ada notifikasi baru
            </div>
            @endif
        </div>
        <a href="/notifications" class="block px-4 py-2.5 text-center text-xs font-semibold transition"
           style="color: var(--color-primary); border-top: 1px solid var(--color-border);">
            View All Notifications →
        </a>
    </div>
</div>
