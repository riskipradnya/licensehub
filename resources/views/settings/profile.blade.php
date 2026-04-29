<x-app-layout title="Profile & Preferences" :breadcrumbs="[['label' => 'Settings', 'url' => '#'], ['label' => 'Profile']]">

    <div class="max-w-3xl mx-auto">
        <h2 class="text-xl font-bold mb-6" style="color: var(--color-text-primary);">Profile & Preferences</h2>

        {{-- PROFILE CARD --}}
        <div class="card mb-6">
            <div class="flex flex-col sm:flex-row items-center gap-6">
                <div class="w-24 h-24 rounded-2xl flex items-center justify-center text-white text-3xl font-bold shrink-0"
                     style="background: linear-gradient(135deg, #6366f1, #8b5cf6);">
                    AR
                </div>
                <div class="flex-1 text-center sm:text-left">
                    <h3 class="text-lg font-bold" style="color: var(--color-text-primary);">Ahmad Rizky</h3>
                    <p class="text-sm" style="color: var(--color-text-secondary);">admin@company.com</p>
                    <div class="flex items-center gap-2 mt-2 justify-center sm:justify-start">
                        <span class="text-xs px-2 py-0.5 rounded-md font-medium" style="background: #6366f120; color: #6366f1;">Super Admin</span>
                        <span class="text-xs" style="color: var(--color-text-secondary);">• IT Department</span>
                    </div>
                </div>
                <button class="btn btn-secondary text-sm">📸 Change Photo</button>
            </div>
        </div>

        {{-- FORM --}}
        <form x-data="{ loading: false }" @submit.prevent="loading = true">
            {{-- Personal Info --}}
            <div class="card mb-6">
                <h3 class="text-sm font-semibold uppercase tracking-wider mb-4" style="color: var(--color-text-secondary);">Personal Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div><label class="form-label">Full Name</label><input type="text" class="form-input" value="Ahmad Rizky"></div>
                    <div><label class="form-label">Email</label><input type="email" class="form-input" value="admin@company.com" disabled style="opacity: 0.6;"></div>
                    <div><label class="form-label">Phone</label><input type="tel" class="form-input" value="+62-812-3456-7890"></div>
                    <div><label class="form-label">Department</label><input type="text" class="form-input" value="IT Department" disabled style="opacity: 0.6;"></div>
                </div>
            </div>

            {{-- Change Password --}}
            <div class="card mb-6">
                <h3 class="text-sm font-semibold uppercase tracking-wider mb-4" style="color: var(--color-text-secondary);">Change Password</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2"><label class="form-label">Current Password</label><input type="password" class="form-input" placeholder="Masukkan password lama"></div>
                    <div><label class="form-label">New Password</label><input type="password" class="form-input" placeholder="Minimal 8 karakter"></div>
                    <div><label class="form-label">Confirm New Password</label><input type="password" class="form-input" placeholder="Ulangi password baru"></div>
                </div>
            </div>

            {{-- Preferences --}}
            <div class="card mb-6">
                <h3 class="text-sm font-semibold uppercase tracking-wider mb-4" style="color: var(--color-text-secondary);">Preferences</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div><p class="text-sm font-medium" style="color: var(--color-text-primary);">Dark Mode</p><p class="text-xs" style="color: var(--color-text-secondary);">Gunakan tampilan gelap</p></div>
                        <button type="button" @click="$store.darkMode.toggle()" class="relative w-12 h-7 rounded-full transition-colors duration-200" :class="$store.darkMode.on ? 'bg-indigo-500' : 'bg-gray-300'">
                            <span class="absolute top-0.5 left-0.5 w-6 h-6 bg-white rounded-full transition-transform duration-200 shadow" :class="$store.darkMode.on ? 'translate-x-5' : ''"></span>
                        </button>
                    </div>
                    <div class="flex items-center justify-between" style="border-top: 1px solid var(--color-border); padding-top: 1rem;">
                        <div><p class="text-sm font-medium" style="color: var(--color-text-primary);">Email Notifications</p><p class="text-xs" style="color: var(--color-text-secondary);">Terima notifikasi via email</p></div>
                        <button type="button" x-data="{ on: true }" @click="on = !on" class="relative w-12 h-7 rounded-full transition-colors duration-200" :class="on ? 'bg-indigo-500' : 'bg-gray-300'">
                            <span class="absolute top-0.5 left-0.5 w-6 h-6 bg-white rounded-full transition-transform duration-200 shadow" :class="on ? 'translate-x-5' : ''"></span>
                        </button>
                    </div>
                    <div class="flex items-center justify-between" style="border-top: 1px solid var(--color-border); padding-top: 1rem;">
                        <div><p class="text-sm font-medium" style="color: var(--color-text-primary);">Alert Threshold</p><p class="text-xs" style="color: var(--color-text-secondary);">Hari sebelum kedaluwarsa untuk alert</p></div>
                        <select class="form-input w-auto text-sm">
                            <option>H-7</option><option>H-14</option><option selected>H-21</option><option>H-30</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex justify-end gap-3">
                <button type="button" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary" :disabled="loading">
                    <span x-text="loading ? 'Saving...' : '💾 Save Changes'">💾 Save Changes</span>
                </button>
            </div>
        </form>
    </div>

</x-app-layout>
