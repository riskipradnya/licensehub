<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Welcome' }} — LicenseHub</title>
    <meta name="description" content="LicenseHub — Sistem Manajemen Lisensi untuk departemen IT & Finance">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased min-h-screen flex items-center justify-center relative overflow-hidden"
      style="background: linear-gradient(135deg, #4f46e5 0%, #1e1b4b 40%, #0f172a 100%);">

    {{-- ANIMATED BACKGROUND PATTERN --}}
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-80 h-80 rounded-full opacity-10"
             style="background: radial-gradient(circle, #818cf8, transparent); animation: pulse 4s infinite;"></div>
        <div class="absolute -bottom-32 -left-32 w-96 h-96 rounded-full opacity-8"
             style="background: radial-gradient(circle, #6366f1, transparent); animation: pulse 6s infinite 1s;"></div>
        <div class="absolute top-1/3 left-1/4 w-64 h-64 rounded-full opacity-5"
             style="background: radial-gradient(circle, #a5b4fc, transparent); animation: pulse 5s infinite 2s;"></div>

        {{-- GRID PATTERN --}}
        <svg class="absolute inset-0 w-full h-full opacity-[0.03]" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse">
                    <path d="M 40 0 L 0 0 0 40" fill="none" stroke="white" stroke-width="0.5"/>
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#grid)" />
        </svg>
    </div>

    {{-- CONTENT --}}
    <div class="relative z-10 w-full max-w-md px-4">
        {{-- LOGO --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl mb-4"
                 style="background: linear-gradient(135deg, #6366f1, #4f46e5); box-shadow: 0 8px 24px rgba(99,102,241,0.4);">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-white tracking-tight">LicenseHub</h1>
            <p class="text-sm mt-1" style="color: #94a3b8;">License Management System</p>
        </div>

        {{-- CARD --}}
        <div class="glass-card p-8">
            {{ $slot }}
        </div>

        {{-- FOOTER TEXT --}}
        @if(isset($footer))
        <div class="text-center mt-6 text-sm" style="color: #94a3b8;">
            {{ $footer }}
        </div>
        @endif
    </div>
</body>
</html>
