<x-app-layout title="Add New License" :breadcrumbs="[['label' => 'License Management', 'url' => '/licenses'], ['label' => 'Add License']]">

    <div class="max-w-3xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xl font-bold" style="color: var(--color-text-primary);">Add New License</h2>
                <p class="text-sm mt-1" style="color: var(--color-text-secondary);">Tambahkan lisensi baru ke inventaris</p>
            </div>
            <a href="/licenses" class="btn btn-secondary text-sm">← Back</a>
        </div>

        <form method="POST" action="/licenses" enctype="multipart/form-data" x-data="{ licenseType: 'subscription', loading: false }" @submit="loading = true" id="create-license-form">
            @csrf

            {{-- SECTION 1: Basic Info --}}
            <div class="card mb-6">
                <h3 class="text-sm font-semibold uppercase tracking-wider mb-4" style="color: var(--color-text-secondary);">Basic Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label for="name" class="form-label">License Name <span style="color: var(--color-status-danger);">*</span></label>
                        <input type="text" id="name" name="name" class="form-input" placeholder="e.g. Microsoft 365 Business" required>
                    </div>
                    <div>
                        <label for="category" class="form-label">Category <span style="color: var(--color-status-danger);">*</span></label>
                        <select id="category" name="category" class="form-input" required>
                            <option value="" disabled selected>Pilih Kategori</option>
                            <option value="os">OS</option>
                            <option value="software">Software</option>
                            <option value="antivirus">Antivirus</option>
                            <option value="security">Security</option>
                        </select>
                    </div>
                    <div>
                        <label for="vendor_id" class="form-label">Vendor <span style="color: var(--color-status-danger);">*</span></label>
                        <select id="vendor_id" name="vendor_id" class="form-input" required>
                            <option value="" disabled selected>Pilih Vendor</option>
                            <option value="1">Microsoft</option>
                            <option value="2">Oracle Corp</option>
                            <option value="3">Adobe Inc.</option>
                            <option value="4">Kaspersky Lab</option>
                            <option value="5">VHP</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">License Type <span style="color: var(--color-status-danger);">*</span></label>
                        <div class="flex gap-4 mt-2">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="type" value="subscription" x-model="licenseType" class="w-4 h-4 text-indigo-500 focus:ring-indigo-400">
                                <span class="text-sm" style="color: var(--color-text-primary);">Subscription</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="type" value="perpetual" x-model="licenseType" class="w-4 h-4 text-indigo-500 focus:ring-indigo-400">
                                <span class="text-sm" style="color: var(--color-text-primary);">Perpetual</span>
                            </label>
                        </div>
                    </div>
                    <div>
                        <label for="serial_key" class="form-label">Serial Key</label>
                        <input type="text" id="serial_key" name="serial_key" class="form-input font-mono" placeholder="XXXX-XXXX-XXXX-XXXX">
                    </div>
                </div>
            </div>

            {{-- SECTION 2: Validity Period --}}
            <div class="card mb-6">
                <h3 class="text-sm font-semibold uppercase tracking-wider mb-4" style="color: var(--color-text-secondary);">Validity Period</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="start_date" class="form-label">Start Date <span style="color: var(--color-status-danger);">*</span></label>
                        <input type="date" id="start_date" name="start_date" class="form-input" required>
                    </div>
                    <div x-show="licenseType === 'subscription'">
                        <label for="expiry_date" class="form-label">Expiry Date <span style="color: var(--color-status-danger);">*</span></label>
                        <input type="date" id="expiry_date" name="expiry_date" class="form-input">
                    </div>
                    <div>
                        <label for="seats" class="form-label">Number of Seats/Users</label>
                        <input type="number" id="seats" name="seats" class="form-input" placeholder="e.g. 50" min="1">
                    </div>
                    <div>
                        <label for="cost" class="form-label">Cost (Rp) <span style="color: var(--color-status-danger);">*</span></label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm" style="color: var(--color-text-secondary);">Rp</span>
                            <input type="number" id="cost" name="cost" class="form-input pl-10" placeholder="0" required min="0">
                        </div>
                    </div>
                </div>
            </div>

            {{-- SECTION 3: Upload --}}
            <div class="card mb-6">
                <h3 class="text-sm font-semibold uppercase tracking-wider mb-4" style="color: var(--color-text-secondary);">Upload Documents</h3>
                <x-file-upload name="documents" accept=".pdf,.doc,.docx" maxSize="10" :multiple="true" />
            </div>

            {{-- SECTION 4: Notes --}}
            <div class="card mb-6">
                <h3 class="text-sm font-semibold uppercase tracking-wider mb-4" style="color: var(--color-text-secondary);">Additional Notes</h3>
                <textarea id="notes" name="notes" rows="3" class="form-input" placeholder="Catatan tambahan mengenai lisensi ini..."></textarea>
            </div>

            {{-- ACTIONS --}}
            <div class="flex items-center justify-end gap-3">
                <a href="/licenses" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary" :disabled="loading" id="save-license-btn">
                    <svg x-show="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <span x-text="loading ? 'Saving...' : '💾 Save License'">💾 Save License</span>
                </button>
            </div>
        </form>
    </div>

</x-app-layout>
