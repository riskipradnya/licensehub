<x-guest-layout>
    <x-slot:title>Forgot Password</x-slot:title>

    <div class="text-center mb-6">
        <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl mb-3"
             style="background: rgba(99,102,241,0.15);">
            <svg class="w-6 h-6" style="color: #a5b4fc;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
            </svg>
        </div>
        <h2 class="text-xl font-bold text-white">Lupa Password?</h2>
        <p class="text-sm mt-1 leading-relaxed" style="color: #94a3b8;">
            Masukkan email Anda dan kami akan mengirimkan link untuk mereset password.
        </p>
    </div>

    {{-- SUCCESS MESSAGE --}}
    @if(session('status'))
        <div class="mb-4 px-4 py-3 rounded-xl text-sm font-medium" style="background: rgba(34,197,94,0.15); color: #86efac; border: 1px solid rgba(34,197,94,0.3);">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="/forgot-password" x-data="{ loading: false }" @submit="loading = true" id="forgot-password-form">
        @csrf

        {{-- EMAIL --}}
        <div class="mb-6">
            <label for="email" class="block text-sm font-medium text-white/80 mb-1.5">Email</label>
            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2">
                    <svg class="w-4.5 h-4.5" style="color: #94a3b8;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
                    </svg>
                </span>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
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

        {{-- SUBMIT --}}
        <button type="submit" id="reset-button"
                class="w-full py-3 rounded-xl text-sm font-semibold text-white tracking-wide uppercase transition-all duration-300 flex items-center justify-center gap-2"
                style="background: linear-gradient(135deg, #6366f1, #4f46e5); box-shadow: 0 4px 16px rgba(99,102,241,0.4);"
                onmouseover="this.style.boxShadow='0 6px 24px rgba(99,102,241,0.6)'; this.style.transform='translateY(-1px)'"
                onmouseout="this.style.boxShadow='0 4px 16px rgba(99,102,241,0.4)'; this.style.transform='translateY(0)'"
                :disabled="loading">
            <svg x-show="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            <span x-text="loading ? 'Mengirim...' : 'Kirim Link Reset'">Kirim Link Reset</span>
        </button>
    </form>

    <x-slot:footer>
        Ingat password?
        <a href="/login" class="font-semibold hover:underline transition" style="color: #a5b4fc;">Kembali ke Login</a>
    </x-slot:footer>
</x-guest-layout>
