<x-app-layout title="Add Vendor" :breadcrumbs="[['label' => 'Vendor Management', 'url' => route('vendors.index')], ['label' => 'Add Vendor']]">
    <div class="max-w-2xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold" style="color: var(--color-text-primary);">Add New Vendor</h2>
            <a href="{{ route('vendors.index') }}" class="btn btn-secondary text-sm">← Back</a>
        </div>
        <form method="POST" action="{{ route('vendors.store') }}" x-data="{ loading: false }" @submit="loading = true" id="create-vendor-form">
            @csrf
            <div class="card mb-6">
                <h3 class="text-sm font-semibold uppercase tracking-wider mb-4" style="color: var(--color-text-secondary);">Vendor Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="form-label">Vendor Name <span style="color: var(--color-status-danger);">*</span></label>
                        <input type="text" name="name" class="form-input @error('name') border-red-500 @enderror" placeholder="e.g. Microsoft" value="{{ old('name') }}" required>
                        @error('name') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="form-label">Contact Person</label>
                        <input type="text" name="contact_person" class="form-input" placeholder="Nama kontak vendor" value="{{ old('contact_person') }}">
                    </div>
                    <div class="md:col-span-2">
                        <label class="form-label">Address</label>
                        <textarea name="address" rows="2" class="form-input" placeholder="Alamat lengkap vendor">{{ old('address') }}</textarea>
                    </div>
                    <div>
                        <label class="form-label">Email Support <span style="color: var(--color-status-danger);">*</span></label>
                        <input type="email" name="email" class="form-input @error('email') border-red-500 @enderror" placeholder="support@vendor.com" value="{{ old('email') }}" required>
                        @error('email') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="form-label">Phone</label>
                        <input type="tel" name="phone" class="form-input" placeholder="+62-xxx-xxxx" value="{{ old('phone') }}">
                    </div>
                    <div class="md:col-span-2">
                        <label class="form-label">Website</label>
                        <input type="url" name="website" class="form-input @error('website') border-red-500 @enderror" placeholder="https://www.vendor.com" value="{{ old('website') }}">
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
                            <option value="24h" @selected(old('sla_response') === '24h')>24 Hours</option>
                            <option value="48h" @selected(old('sla_response') === '48h')>48 Hours</option>
                            <option value="72h" @selected(old('sla_response') === '72h')>72 Hours</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Support Hours</label>
                        <select name="sla_hours" class="form-input">
                            <option value="">— Pilih —</option>
                            <option value="24/7" @selected(old('sla_hours') === '24/7')>24/7</option>
                            <option value="business" @selected(old('sla_hours') === 'business')>Business Hours (9-17)</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="card mb-6">
                <h3 class="text-sm font-semibold uppercase tracking-wider mb-4" style="color: var(--color-text-secondary);">Bank Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Bank Name</label>
                        <input type="text" name="bank_name" class="form-input" placeholder="e.g. BCA, Mandiri, BNI" value="{{ old('bank_name') }}">
                    </div>
                    <div>
                        <label class="form-label">Account Number</label>
                        <input type="text" name="bank_account_number" class="form-input font-mono" placeholder="e.g. 1234567890" value="{{ old('bank_account_number') }}">
                    </div>
                </div>
            </div>
            <div class="card mb-6">
                <h3 class="text-sm font-semibold uppercase tracking-wider mb-4" style="color: var(--color-text-secondary);">Notes</h3>
                <textarea name="notes" rows="3" class="form-input" placeholder="Catatan tambahan...">{{ old('notes') }}</textarea>
            </div>
            <div class="flex justify-end gap-3">
                <a href="{{ route('vendors.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary" :disabled="loading"><span x-text="loading ? 'Saving...' : '💾 Save Vendor'">💾 Save Vendor</span></button>
            </div>
        </form>
    </div>
</x-app-layout>
