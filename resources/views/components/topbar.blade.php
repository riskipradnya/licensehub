@props(['title' => 'Dashboard'])

<header class="topbar sticky top-0 z-30">
    <div class="flex items-center gap-4">
        {{-- HAMBURGER (mobile) --}}
        <button @click="$store.sidebar.toggle()" class="btn-ghost p-2 rounded-lg" id="sidebar-toggle">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>

        {{-- PAGE TITLE --}}
        <h1 class="text-lg font-semibold tracking-tight" style="color: var(--color-text-primary);">
            {{ $title }}
        </h1>
    </div>

    <div class="flex items-center gap-3">
        {{-- SEARCH --}}
        <div class="hidden md:flex items-center relative">
            <svg class="w-4 h-4 absolute left-3" style="color: var(--color-text-secondary);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" placeholder="Search licenses, vendors..."
                   class="form-input pl-10 w-64 text-sm py-2" id="global-search">
        </div>

        {{-- DARK MODE TOGGLE --}}
        <button @click="$store.darkMode.toggle()" class="btn-ghost p-2 rounded-lg" id="dark-mode-toggle"
                title="Toggle Dark Mode">
            {{-- Sun icon --}}
            <svg x-show="$store.darkMode.on" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            {{-- Moon icon --}}
            <svg x-show="!$store.darkMode.on" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
            </svg>
        </button>

        {{-- NOTIFICATIONS --}}
        <x-notification-bell :count="3" />

        {{-- USER DROPDOWN --}}
        <div x-data="dropdown" class="relative">
            <button @click="toggle()" class="flex items-center gap-2 p-1 rounded-lg hover:bg-gray-100 transition" id="user-menu-button">
                <div class="w-8 h-8 rounded-full overflow-hidden flex items-center justify-center border-2 border-indigo-100 bg-gray-100 shrink-0">
                    <img src="{{ auth()->user()->avatar ? asset('storage/' . auth()->user()->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name ?? 'A') . '&color=4f46e5&background=e0e7ff' }}" alt="Avatar" class="w-full h-full object-cover">
                </div>
                <span class="hidden md:block text-sm font-medium" style="color: var(--color-text-primary);">
                    {{ auth()->user()->name ?? 'Admin' }}
                </span>
                <svg class="w-4 h-4 hidden md:block" style="color: var(--color-text-secondary);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <div x-show="open" @click.away="close()" x-transition
                 class="absolute right-0 mt-2 w-48 rounded-xl shadow-lg py-1 z-50"
                 style="background: var(--color-card-bg); border: 1px solid var(--color-border);"
                 id="user-dropdown-menu">
                <a href="/profile" class="block px-4 py-2 text-sm hover:bg-gray-50 transition"
                   style="color: var(--color-text-primary);">Profile & Preferences</a>
                <hr style="border-color: var(--color-border);">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm hover:bg-gray-50 transition" style="color: #ef4444;">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
