<x-app-layout title="Edit License" :breadcrumbs="[['label' => 'License Management', 'url' => '/licenses'], ['label' => 'Edit License']]">

    <div class="max-w-3xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xl font-bold" style="color: var(--color-text-primary);">Edit License</h2>
                <p class="text-sm mt-1" style="color: var(--color-text-secondary);">Perbarui informasi lisensi</p>
            </div>
            <a href="/licenses" class="btn btn-secondary text-sm">← Back</a>
        </div>

        <form method="POST" action="/licenses/1" enctype="multipart/form-data" x-data="{ licenseType: 'subscription', loading: false }" @submit="loading = true" id="edit-license-form">
            @csrf
            @method('PUT')

            {{-- Basic Info --}}
            <div class="card mb-6">
                <h3 class="text-sm font-semibold uppercase tracking-wider mb-4" style="color: var(--color-text-secondary);">Basic Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label for="name" class="form-label">License Name *</label>
                        <input type="text" id="name" name="name" class="form-input" value="Microsoft 365 Business" required>
                    </div>
                    <div>
                        <label for="category" class="form-label">Category *</label>
                        <select id="category" name="category" class="form-input" required>
                            <option value="os">OS</option>
                            <option value="software" selected>Software</option>
                            <option value="antivirus">Antivirus</option>
                            <option value="security">Security</option>
                        </select>
                    </div>
                    <div>
                        <label for="vendor_id" class="form-label">Vendor *</label>
                        <select id="vendor_id" name="vendor_id" class="form-input" required>
                            <option value="1" selected>Microsoft</option>
                            <option value="2">Oracle Corp</option>
                            <option value="3">Adobe Inc.</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">License Type *</label>
                        <div class="flex gap-4 mt-2">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="type" value="subscription" x-model="licenseType" class="w-4 h-4 text-indigo-500">
                                <span class="text-sm">Subscription</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="type" value="perpetual" x-model="licenseType" class="w-4 h-4 text-indigo-500">
                                <span class="text-sm">Perpetual</span>
                            </label>
                        </div>
                    </div>
                    <div>
                        <label for="serial_key" class="form-label">Serial Key</label>
                        <input type="text" id="serial_key" name="serial_key" class="form-input font-mono" value="ABCD-EFGH-IJKL-MNOP">
                    </div>
                </div>
            </div>

            {{-- Validity --}}
            <div class="card mb-6">
                <h3 class="text-sm font-semibold uppercase tracking-wider mb-4" style="color: var(--color-text-secondary);">Validity Period</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div><label for="start_date" class="form-label">Start Date *</label><input type="date" id="start_date" name="start_date" class="form-input" value="2025-04-28" required></div>
                    <div x-show="licenseType === 'subscription'"><label for="expiry_date" class="form-label">Expiry Date *</label><input type="date" id="expiry_date" name="expiry_date" class="form-input" value="2026-04-28"></div>
                    <div><label for="seats" class="form-label">Number of Seats</label><input type="number" id="seats" name="seats" class="form-input" value="50"></div>
                    <div><label for="cost" class="form-label">Cost (Rp) *</label><div class="relative"><span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm" style="color: var(--color-text-secondary);">Rp</span><input type="number" id="cost" name="cost" class="form-input pl-10" value="12000000" required></div></div>
                </div>
            </div>

            {{-- Existing Documents --}}
            <div class="card mb-6">
                <h3 class="text-sm font-semibold uppercase tracking-wider mb-4" style="color: var(--color-text-secondary);">Documents</h3>
                <div class="space-y-2 mb-4">
                    <div class="flex items-center gap-3 p-3 rounded-lg" style="background: var(--color-content-bg); border: 1px solid var(--color-border);">
                        <svg class="w-5 h-5 shrink-0" style="color: var(--color-primary);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                        <div class="flex-1"><p class="text-sm font-medium">Kontrak_MS365_2025.pdf</p><p class="text-xs" style="color: var(--color-text-secondary);">2.1 MB</p></div>
                        <button type="button" class="btn-ghost p-1 rounded text-xs" style="color: var(--color-status-danger);">Remove</button>
                    </div>
                </div>
                <x-file-upload name="documents" accept=".pdf,.doc,.docx" maxSize="10" :multiple="true" />
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-between">
                <button type="button" class="btn btn-danger text-sm" onclick="confirm('Hapus lisensi ini?')">🗑 Delete License</button>
                <div class="flex gap-3">
                    <a href="/licenses" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary" :disabled="loading">
                        <span x-text="loading ? 'Saving...' : '💾 Update License'">💾 Update License</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

</x-app-layout>
