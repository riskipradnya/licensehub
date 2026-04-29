<x-guest-layout>
    <x-slot:title>Register</x-slot:title>

    <div class="text-center mb-6">
        <h2 class="text-xl font-bold text-white">Buat Akun Baru</h2>
        <p class="text-sm mt-1" style="color: #94a3b8;">Daftarkan akun untuk mengakses LicenseHub</p>
    </div>

    <form method="POST" action="/register" x-data="{ showPassword: false, loading: false }" @submit="loading = true" id="register-form">
        @csrf

        {{-- NAME --}}
        <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-white/80 mb-1.5">Nama Lengkap</label>
            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2">
                    <svg class="w-4.5 h-4.5" style="color: #94a3b8;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                    </svg>
                </span>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                       placeholder="Masukkan nama lengkap"
                       class="w-full pl-10 pr-4 py-3 rounded-xl text-sm text-white placeholder-white/40 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-400/50"
                       style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.12);"
                       onfocus="this.style.borderColor='rgba(99,102,241,0.6)'"
                       onblur="this.style.borderColor='rgba(255,255,255,0.12)'">
            </div>
            @error('name')
                <p class="mt-1 text-xs" style="color: #fca5a5;">{{ $message }}</p>
            @enderror
        </div>

        {{-- EMAIL --}}
        <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-white/80 mb-1.5">Email</label>
            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2">
                    <svg class="w-4.5 h-4.5" style="color: #94a3b8;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
                    </svg>
                </span>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required
                       placeholder="nama@perusahaan.com"
                       class="w-full pl-10 pr-4 py-3 rounded-xl text-sm text-white placeholder-white/40 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-400/50"
                       style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.12);"
                       onfocus="this.style.borderColor='rgba(99,102,241,0.6)'"
                       onblur="this.style.borderColor='rgba(255,255,255,0.12)'">
            </div>
            @error('email')
                <p class="mt-1 text-xs" style="color: #fca5a5;">{{ $message }}</p>
            @enderror
        </div>

        {{-- ROLE --}}
        <div class="mb-4">
            <label for="role" class="block text-sm font-medium text-white/80 mb-1.5">Role / Jabatan</label>
            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2">
                    <svg class="w-4.5 h-4.5" style="color: #94a3b8;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5zm6-10.125a1.875 1.875 0 11-3.75 0 1.875 1.875 0 013.75 0zm1.294 6.336a6.721 6.721 0 01-3.17.789 6.721 6.721 0 01-3.168-.789 3.376 3.376 0 016.338 0z"/>
                    </svg>
                </span>
                <select id="role" name="role" required
                        class="w-full pl-10 pr-4 py-3 rounded-xl text-sm text-white appearance-none cursor-pointer transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-400/50"
                        style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.12);">
                    <option value="" disabled selected style="background: #1e293b;">Pilih Role</option>
                    <option value="it_staff" style="background: #1e293b;">IT Staff</option>
                    <option value="finance_manager" style="background: #1e293b;">Finance Manager</option>
                    <option value="finance_staff" style="background: #1e293b;">Finance Staff</option>
                </select>
            </div>
            @error('role')
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
                       placeholder="Minimal 8 karakter"
                       class="w-full pl-10 pr-12 py-3 rounded-xl text-sm text-white placeholder-white/40 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-400/50"
                       style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.12);"
                       onfocus="this.style.borderColor='rgba(99,102,241,0.6)'"
                       onblur="this.style.borderColor='rgba(255,255,255,0.12)'">
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

        {{-- CONFIRM PASSWORD --}}
        <div class="mb-6">
            <label for="password_confirmation" class="block text-sm font-medium text-white/80 mb-1.5">Konfirmasi Password</label>
            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2">
                    <svg class="w-4.5 h-4.5" style="color: #94a3b8;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
                    </svg>
                </span>
                <input type="password" id="password_confirmation" name="password_confirmation" required
                       placeholder="Ulangi password"
                       class="w-full pl-10 pr-4 py-3 rounded-xl text-sm text-white placeholder-white/40 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-400/50"
                       style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.12);"
                       onfocus="this.style.borderColor='rgba(99,102,241,0.6)'"
                       onblur="this.style.borderColor='rgba(255,255,255,0.12)'">
            </div>
        </div>

        {{-- SUBMIT --}}
        <button type="submit" id="register-button"
                class="w-full py-3 rounded-xl text-sm font-semibold text-white tracking-wide uppercase transition-all duration-300 flex items-center justify-center gap-2"
                style="background: linear-gradient(135deg, #6366f1, #4f46e5); box-shadow: 0 4px 16px rgba(99,102,241,0.4);"
                onmouseover="this.style.boxShadow='0 6px 24px rgba(99,102,241,0.6)'; this.style.transform='translateY(-1px)'"
                onmouseout="this.style.boxShadow='0 4px 16px rgba(99,102,241,0.4)'; this.style.transform='translateY(0)'"
                :disabled="loading">
            <svg x-show="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            <span x-text="loading ? 'Memproses...' : 'Daftar Akun'">Daftar Akun</span>
        </button>
    </form>

    <x-slot:footer>
        Sudah punya akun?
        <a href="/login" class="font-semibold hover:underline transition" style="color: #a5b4fc;">Masuk di sini</a>
    </x-slot:footer>
</x-guest-layout>
