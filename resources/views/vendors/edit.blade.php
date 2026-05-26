<x-app-layout title="Edit Vendor" :breadcrumbs="[['label' => 'Vendor Management', 'url' => route('vendors.index')], ['label' => $vendor->name, 'url' => route('vendors.show', $vendor)], ['label' => 'Edit']]">
    <div class="max-w-2xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold" style="color: var(--color-text-primary);">Edit Vendor</h2>
            <a href="{{ route('vendors.show', $vendor) }}" class="btn btn-secondary text-sm">← Back</a>
        </div>
        <form method="POST" action="{{ route('vendors.update', $vendor) }}" enctype="multipart/form-data" x-data="{ loading: false }" @submit="loading = true" id="edit-vendor-form">
            @csrf @method('PUT')
            <div class="card mb-6">
                <h3 class="text-sm font-semibold uppercase tracking-wider mb-4" style="color: var(--color-text-secondary);">Vendor Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="form-label">Vendor Name <span style="color: var(--color-status-danger);">*</span></label>
                        <input type="text" name="name" class="form-input @error('name') border-red-500 @enderror" value="{{ old('name', $vendor->name) }}" required>
                        @error('name') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="form-label">Contact Person</label>
                        <input type="text" name="contact_person" class="form-input" value="{{ old('contact_person', $vendor->contact_person) }}">
                    </div>
                    <div class="md:col-span-2">
                        <label class="form-label">Address</label>
                        <textarea name="address" rows="2" class="form-input">{{ old('address', $vendor->address) }}</textarea>
                    </div>
                    <div>
                        <label class="form-label">Email Support <span style="color: var(--color-status-danger);">*</span></label>
                        <input type="email" name="email" class="form-input @error('email') border-red-500 @enderror" value="{{ old('email', $vendor->email) }}" required>
                        @error('email') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="form-label">Phone</label>
                        <input type="tel" name="phone" class="form-input" value="{{ old('phone', $vendor->phone) }}">
                    </div>
                    <div class="md:col-span-2">
                        <label class="form-label">Website</label>
                        <input type="url" name="website" class="form-input @error('website') border-red-500 @enderror" value="{{ old('website', $vendor->website) }}">
                        @error('website') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
            <div class="card mb-6">
                <h3 class="text-sm font-semibold uppercase tracking-wider mb-4" style="color: var(--color-text-secondary);">SLA Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Response Time</label>
                        <select name="sla_response" class="form-input">
                            <option value="">— Pilih —</option>
                            <option value="24h" @selected(old('sla_response', $vendor->sla_response) === '24h')>24 Hours</option>
                            <option value="48h" @selected(old('sla_response', $vendor->sla_response) === '48h')>48 Hours</option>
                            <option value="72h" @selected(old('sla_response', $vendor->sla_response) === '72h')>72 Hours</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Support Hours</label>
                        <select name="sla_hours" class="form-input">
                            <option value="">— Pilih —</option>
                            <option value="24/7" @selected(old('sla_hours', $vendor->sla_hours) === '24/7')>24/7</option>
                            <option value="business" @selected(old('sla_hours', $vendor->sla_hours) === 'business')>Business Hours (9-17)</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="card mb-6">
                <h3 class="text-sm font-semibold uppercase tracking-wider mb-4" style="color: var(--color-text-secondary);">Bank Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Bank Name</label>
                        <select name="bank_name" class="form-input">
                            <option value="">— Pilih Bank —</option>
                            @foreach(\App\Http\Controllers\XenditController::BANK_CODES as $code => $label)
                                <option value="{{ $code }}" @selected(old('bank_name', $vendor->bank_name) === $code)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('bank_name') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="form-label">Account Number</label>
                        <input type="text" name="bank_account_number" class="form-input font-mono" placeholder="e.g. 1234567890" value="{{ old('bank_account_number', $vendor->bank_account_number) }}">
                    </div>
                </div>
            </div>
            <div class="card mb-6">
                <h3 class="text-sm font-semibold uppercase tracking-wider mb-4" style="color: var(--color-text-secondary);">Notes</h3>
                <textarea name="notes" rows="3" class="form-input">{{ old('notes', $vendor->notes) }}</textarea>
            </div>
            <div class="card mb-6">
                <h3 class="text-sm font-semibold uppercase tracking-wider mb-4" style="color: var(--color-text-secondary);">Vendor Documents</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="form-label">Vendor Logo (Image)</label>
                        @if($vendor->logo)
                            <div class="mb-2">
                                <img src="{{ Storage::url($vendor->logo) }}" alt="Logo" class="h-12 object-contain rounded">
                            </div>
                        @endif
                        <input type="file" name="logo" accept="image/*" class="form-input @error('logo') border-red-500 @enderror">
                        @error('logo') <p class="form-error">{{ $message }}</p> @enderror
                        <p class="text-xs mt-1" style="color: var(--color-text-secondary);">Max 2MB. Format: JPG, PNG.</p>
                    </div>
                    <div>
                        <label class="form-label">MSA File (PDF)</label>
                        @if($vendor->msa_file)
                            <div class="mb-2">
                                <a href="{{ Storage::url($vendor->msa_file) }}" target="_blank" class="text-xs hover:underline" style="color: var(--color-primary);">📄 View Current MSA</a>
                            </div>
                        @endif
                        <input type="file" name="msa_file" accept=".pdf" class="form-input @error('msa_file') border-red-500 @enderror">
                        @error('msa_file') <p class="form-error">{{ $message }}</p> @enderror
                        <p class="text-xs mt-1" style="color: var(--color-text-secondary);">Max 10MB.</p>
                    </div>
                    <div>
                        <label class="form-label">SLA File (PDF)</label>
                        @if($vendor->sla_file)
                            <div class="mb-2">
                                <a href="{{ Storage::url($vendor->sla_file) }}" target="_blank" class="text-xs hover:underline" style="color: var(--color-primary);">📄 View Current SLA</a>
                            </div>
                        @endif
                        <input type="file" name="sla_file" accept=".pdf" class="form-input @error('sla_file') border-red-500 @enderror">
                        @error('sla_file') <p class="form-error">{{ $message }}</p> @enderror
                        <p class="text-xs mt-1" style="color: var(--color-text-secondary);">Max 10MB.</p>
                    </div>
                </div>
            </div>
        </form>
        <div class="flex items-center justify-between">
            <div x-data="{ confirmDelete: false }">
                <button type="button" class="btn btn-danger text-sm" x-show="!confirmDelete" @click="confirmDelete = true">🗑 Delete Vendor</button>
                <div x-show="confirmDelete" class="flex items-center gap-2" x-transition>
                    <span class="text-sm" style="color: var(--color-status-danger);">Yakin hapus?</span>
                    <form method="POST" action="{{ route('vendors.destroy', $vendor) }}" class="inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger text-sm">Ya, Hapus</button>
                    </form>
                    <button type="button" class="btn btn-secondary text-sm" @click="confirmDelete = false">Batal</button>
                </div>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('vendors.show', $vendor) }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" form="edit-vendor-form" class="btn btn-primary px-4 py-2 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </div>
    </div>
</x-app-layout>
