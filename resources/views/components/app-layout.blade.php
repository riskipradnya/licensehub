@props(['title' => 'Dashboard', 'metaDescription' => null, 'breadcrumbs' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data x-bind:data-theme="$store.darkMode.on ? 'dark' : 'light'">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} — LicenseHub</title>
    <meta name="description" content="{{ $metaDescription ?? 'LicenseHub — Sistem Manajemen Lisensi' }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased" x-init="$store.darkMode.init()" style="background: var(--color-content-bg);">

    {{-- MOBILE OVERLAY --}}
    <div x-show="$store.sidebar.open && window.innerWidth < 1024"
         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
         x-transition:leave="transition ease-in duration-200" x-transition:leave-end="opacity-0"
         @click="$store.sidebar.close()"
         class="fixed inset-0 bg-black/50 backdrop-blur-sm z-40 lg:hidden" style="display:none;"></div>

    <div class="flex min-h-screen">
        <x-sidebar />

        <div class="flex-1 flex flex-col min-h-screen transition-all duration-300"
             :class="$store.sidebar.collapsed ? 'lg:ml-[72px]' : 'lg:ml-[260px]'">

            <x-topbar :title="$title" />

            @if($breadcrumbs)
            <div class="px-6 pt-4">
                <x-breadcrumb :items="$breadcrumbs" />
            </div>
            @endif

            <main class="flex-1 p-6">
                {{ $slot }}
            </main>

            <footer class="px-6 py-4 text-center text-xs" style="color: var(--color-text-secondary); border-top: 1px solid var(--color-border);">
                &copy; {{ date('Y') }} LicenseHub — License Management System v1.0
            </footer>
        </div>
    </div>

    <x-toast />
    @stack('scripts')
</body>
</html>
