<x-guest-layout>
    <x-slot:title>Login</x-slot:title>

    <div class="text-center mb-6">
        <h2 class="text-xl font-bold text-white">Selamat Datang</h2>
        <p class="text-sm mt-1" style="color: #94a3b8;">Masuk ke akun Anda untuk melanjutkan</p>
    </div>

    {{-- SESSION ERROR --}}
    @if(session('error'))
        <div class="mb-4 px-4 py-3 rounded-xl text-sm font-medium" style="background: rgba(239,68,68,0.15); color: #fca5a5; border: 1px solid rgba(239,68,68,0.3);">
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="/login" x-data="{ showPassword: false, loading: false }" @submit="loading = true" id="login-form">
        @csrf

        {{-- EMAIL --}}
        <div class="mb-4">
            <label for="username" class="block text-sm font-medium text-white/80 mb-1.5">Username</label>
            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2">
                    <svg class="w-4.5 h-4.5" style="color: #94a3b8;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
                    </svg>
                </span>
                <input type="text" id="username" name="username" value="{{ old('username') }}" required autofocus
                       placeholder="admin"
                       class="w-full pl-10 pr-4 py-3 rounded-xl text-sm text-white placeholder-white/40 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-400/50"
                       style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.12);"
                       onfocus="this.style.borderColor='rgba(99,102,241,0.6)'; this.style.background='rgba(255,255,255,0.12)'"
                       onblur="this.style.borderColor='rgba(255,255,255,0.12)'; this.style.background='rgba(255,255,255,0.08)'">
            </div>
            @error('username')
                <p class="mt-1 text-xs" style="color: #fca5a5;">{{ $message }}</p>
            @enderror
        </div>

        {{-- PASSWORD --}}
        <div class="mb-4">
            <label for="password" class="block text-sm font-medium text-white/80 mb-1.5">Password</label>
            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2">
                    <svg class="w-4.5 h-4.5" style="color: #94a3b8;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                    </svg>
                </span>
                <input :type="showPassword ? 'text' : 'password'" id="password" name="password" required
                       placeholder="••••••••"
                       class="w-full pl-10 pr-12 py-3 rounded-xl text-sm text-white placeholder-white/40 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-400/50"
                       style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.12);"
                       onfocus="this.style.borderColor='rgba(99,102,241,0.6)'; this.style.background='rgba(255,255,255,0.12)'"
                       onblur="this.style.borderColor='rgba(255,255,255,0.12)'; this.style.background='rgba(255,255,255,0.08)'">
                <button type="button" @click="showPassword = !showPassword"
                        class="absolute right-3 top-1/2 -translate-y-1/2 p-1 rounded hover:bg-white/10 transition">
                    <svg x-show="!showPassword" class="w-4 h-4" style="color: #94a3b8;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <svg x-show="showPassword" class="w-4 h-4" style="color: #94a3b8;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/>
                    </svg>
                </button>
            </div>
            @error('password')
                <p class="mt-1 text-xs" style="color: #fca5a5;">{{ $message }}</p>
            @enderror
        </div>

        {{-- REMEMBER --}}
        <div class="flex items-center mb-6">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="remember" id="remember"
                       class="w-4 h-4 rounded border-white/20 bg-white/10 text-indigo-500 focus:ring-indigo-400/50 focus:ring-offset-0">
                <span class="text-sm text-white/70">Ingat saya</span>
            </label>
        </div>

        {{-- SUBMIT --}}
        <button type="submit" id="login-button"
                class="w-full py-3 rounded-xl text-sm font-semibold text-white tracking-wide uppercase transition-all duration-300 flex items-center justify-center gap-2"
                style="background: linear-gradient(135deg, #6366f1, #4f46e5); box-shadow: 0 4px 16px rgba(99,102,241,0.4);"
                onmouseover="this.style.boxShadow='0 6px 24px rgba(99,102,241,0.6)'; this.style.transform='translateY(-1px)'"
                onmouseout="this.style.boxShadow='0 4px 16px rgba(99,102,241,0.4)'; this.style.transform='translateY(0)'"
                :disabled="loading">
            <svg x-show="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            <span x-text="loading ? 'Memproses...' : 'Masuk'">Masuk</span>
        </button>
    </form>


</x-guest-layout>
