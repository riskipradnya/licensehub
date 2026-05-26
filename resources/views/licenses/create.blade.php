<x-app-layout title="Add New License" :breadcrumbs="[['label' => 'License Management', 'url' => route('licenses.index')], ['label' => 'Add License']]">

    <div class="max-w-3xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xl font-bold" style="color: var(--color-text-primary);">Add New License</h2>
                <p class="text-sm mt-1" style="color: var(--color-text-secondary);">Tambahkan lisensi baru ke inventaris</p>
            </div>
            <a href="{{ route('licenses.index') }}" class="btn btn-secondary text-sm">← Back</a>
        </div>

        <form method="POST" action="{{ route('licenses.store') }}" enctype="multipart/form-data" x-data="{ licenseType: '{{ old('type', 'subscription') }}', loading: false, docs: [{id: Date.now()}] }" @submit="loading = true" id="create-license-form">
            @csrf

            {{-- SECTION 1: Basic Info --}}
            <div class="card mb-6">
                <h3 class="text-sm font-semibold uppercase tracking-wider mb-4" style="color: var(--color-text-secondary);">Basic Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label for="name" class="form-label">License Name <span style="color: var(--color-status-danger);">*</span></label>
                        <input type="text" id="name" name="name" class="form-input @error('name') border-red-500 @enderror" placeholder="e.g. Microsoft 365 Business" value="{{ old('name') }}" required>
                        @error('name') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="category_id" class="form-label">Category <span style="color: var(--color-status-danger);">*</span></label>
                        <select id="category_id" name="category_id" class="form-input @error('category_id') border-red-500 @enderror" required>
                            <option value="" disabled @selected(!old('category_id'))>Pilih Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" @selected(old('category_id') == $cat->id)>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="vendor_id" class="form-label">Vendor <span style="color: var(--color-status-danger);">*</span></label>
                        <select id="vendor_id" name="vendor_id" class="form-input @error('vendor_id') border-red-500 @enderror" required>
                            <option value="" disabled @selected(!old('vendor_id'))>Pilih Vendor</option>
                            @foreach($vendors as $vendor)
                                <option value="{{ $vendor->id }}" @selected(old('vendor_id') == $vendor->id)>{{ $vendor->name }}</option>
                            @endforeach
                        </select>
                        @error('vendor_id') <p class="form-error">{{ $message }}</p> @enderror
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
                        <input type="text" id="serial_key" name="serial_key" class="form-input font-mono" placeholder="XXXX-XXXX-XXXX-XXXX" value="{{ old('serial_key') }}">
                    </div>
                </div>
            </div>

            {{-- SECTION 2: Validity Period --}}
            <div class="card mb-6">
                <h3 class="text-sm font-semibold uppercase tracking-wider mb-4" style="color: var(--color-text-secondary);">Validity Period</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="start_date" class="form-label">Start Date <span style="color: var(--color-status-danger);">*</span></label>
                        <input type="date" id="start_date" name="start_date" class="form-input @error('start_date') border-red-500 @enderror" value="{{ old('start_date') }}" required>
                        @error('start_date') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div x-show="licenseType === 'subscription'">
                        <label for="expiry_date" class="form-label">Expiry Date <span style="color: var(--color-status-danger);">*</span></label>
                        <input type="date" id="expiry_date" name="expiry_date" class="form-input @error('expiry_date') border-red-500 @enderror" value="{{ old('expiry_date') }}">
                        @error('expiry_date') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="seats" class="form-label">Number of Seats/Users</label>
                        <input type="number" id="seats" name="seats" class="form-input" placeholder="e.g. 50" min="1" value="{{ old('seats') }}">
                    </div>
                    <div>
                        <label for="cost" class="form-label">Cost (Rp) <span style="color: var(--color-status-danger);">*</span></label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm" style="color: var(--color-text-secondary);">Rp</span>
                            <input type="number" id="cost" name="cost" class="form-input !pl-10 @error('cost') border-red-500 @enderror" placeholder="0" value="{{ old('cost') }}" required min="0">
                        </div>
                        @error('cost') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="billing_cycle" class="form-label">Billing Cycle <span style="color: var(--color-status-danger);">*</span></label>
                        <select id="billing_cycle" name="billing_cycle" class="form-input" required>
                            <option value="yearly" @selected(old('billing_cycle', 'yearly') === 'yearly')>Yearly</option>
                            <option value="monthly" @selected(old('billing_cycle') === 'monthly')>Monthly</option>
                            <option value="quarterly" @selected(old('billing_cycle') === 'quarterly')>Quarterly</option>
                            <option value="one_time" @selected(old('billing_cycle') === 'one_time')>One Time</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- SECTION 3: Notes --}}
            <div class="card mb-6">
                <h3 class="text-sm font-semibold uppercase tracking-wider mb-4" style="color: var(--color-text-secondary);">Additional Notes</h3>
                <textarea id="notes" name="notes" rows="3" class="form-input" placeholder="Catatan tambahan mengenai lisensi ini...">{{ old('notes') }}</textarea>
            </div>

            {{-- SECTION 4: Supporting Documents --}}
            <div class="card mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold uppercase tracking-wider" style="color: var(--color-text-secondary);">Dokumen Pendukung</h3>
                    <button type="button" @click="docs.push({id: Date.now()})" class="btn btn-secondary text-xs">+ Tambah Dokumen</button>
                </div>
                
                <div class="space-y-3">
                    <template x-for="(doc, index) in docs" :key="doc.id">
                        <div class="flex items-start gap-3 p-3 rounded-lg bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                            <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <label class="form-label text-xs">Tipe Dokumen</label>
                                    <select :name="`documents[${index}][type]`" class="form-input py-1.5 text-sm" required>
                                        <option value="contract">Contract</option>
                                        <option value="invoice">Invoice</option>
                                        <option value="certificate">Certificate</option>
                                        <option value="quotation">Quotation</option>
                                        <option value="other" selected>Other</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="form-label text-xs">Pilih File</label>
                                    <input type="file" :name="`documents[${index}][file]`" accept=".pdf,.docx,.zip,.png,.jpg,.jpeg" class="form-input py-1 text-sm" required>
                                </div>
                            </div>
                            <button type="button" @click="docs.splice(index, 1)" class="btn-ghost text-red-500 mt-6" title="Hapus baris">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </template>
                </div>
                <p class="text-xs mt-3" style="color: var(--color-text-secondary);">Maks 10MB per file. Format didukung: PDF, DOCX, ZIP, PNG, JPG.</p>
            </div>

            {{-- ACTIONS --}}
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('licenses.index') }}" class="btn btn-secondary">Cancel</a>
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
