<x-app-layout title="Edit License" :breadcrumbs="[['label' => 'License Management', 'url' => route('licenses.index')], ['label' => $license->name, 'url' => route('licenses.show', $license)], ['label' => 'Edit']]">

    <div class="max-w-3xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xl font-bold" style="color: var(--color-text-primary);">Edit License</h2>
                <p class="text-sm mt-1" style="color: var(--color-text-secondary);">Perbarui informasi lisensi</p>
            </div>
            <a href="{{ route('licenses.show', $license) }}" class="btn btn-secondary text-sm">← Back</a>
        </div>

        <form method="POST" action="{{ route('licenses.update', $license) }}" enctype="multipart/form-data" x-data="{ licenseType: '{{ old('type', $license->type) }}', loading: false }" @submit="loading = true" id="edit-license-form">
            @csrf
            @method('PUT')

            {{-- Basic Info --}}
            <div class="card mb-6">
                <h3 class="text-sm font-semibold uppercase tracking-wider mb-4" style="color: var(--color-text-secondary);">Basic Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label for="name" class="form-label">License Name <span style="color: var(--color-status-danger);">*</span></label>
                        <input type="text" id="name" name="name" class="form-input @error('name') border-red-500 @enderror" value="{{ old('name', $license->name) }}" required>
                        @error('name') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="category_id" class="form-label">Category <span style="color: var(--color-status-danger);">*</span></label>
                        <select id="category_id" name="category_id" class="form-input @error('category_id') border-red-500 @enderror" required>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" @selected(old('category_id', $license->category_id) == $cat->id)>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="vendor_id" class="form-label">Vendor <span style="color: var(--color-status-danger);">*</span></label>
                        <select id="vendor_id" name="vendor_id" class="form-input @error('vendor_id') border-red-500 @enderror" required>
                            @foreach($vendors as $vendor)
                                <option value="{{ $vendor->id }}" @selected(old('vendor_id', $license->vendor_id) == $vendor->id)>{{ $vendor->name }}</option>
                            @endforeach
                        </select>
                        @error('vendor_id') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="form-label">License Type <span style="color: var(--color-status-danger);">*</span></label>
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
                        <input type="text" id="serial_key" name="serial_key" class="form-input font-mono" value="{{ old('serial_key', $license->serial_key) }}">
                    </div>
                </div>
            </div>

            {{-- Validity --}}
            <div class="card mb-6">
                <h3 class="text-sm font-semibold uppercase tracking-wider mb-4" style="color: var(--color-text-secondary);">Validity Period</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="start_date" class="form-label">Start Date <span style="color: var(--color-status-danger);">*</span></label>
                        <input type="date" id="start_date" name="start_date" class="form-input @error('start_date') border-red-500 @enderror" value="{{ old('start_date', $license->start_date->format('Y-m-d')) }}" required>
                        @error('start_date') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div x-show="licenseType === 'subscription'">
                        <label for="expiry_date" class="form-label">Expiry Date <span style="color: var(--color-status-danger);">*</span></label>
                        <input type="date" id="expiry_date" name="expiry_date" class="form-input @error('expiry_date') border-red-500 @enderror" value="{{ old('expiry_date', $license->expiry_date?->format('Y-m-d')) }}">
                        @error('expiry_date') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="seats" class="form-label">Number of Seats</label>
                        <input type="number" id="seats" name="seats" class="form-input" value="{{ old('seats', $license->seats) }}">
                    </div>
                    <div>
                        <label for="cost" class="form-label">Cost (Rp) <span style="color: var(--color-status-danger);">*</span></label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm" style="color: var(--color-text-secondary);">Rp</span>
                            <input type="number" id="cost" name="cost" class="form-input pl-10 @error('cost') border-red-500 @enderror" value="{{ old('cost', (int)$license->cost) }}" required>
                        </div>
                        @error('cost') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="billing_cycle" class="form-label">Billing Cycle <span style="color: var(--color-status-danger);">*</span></label>
                        <select id="billing_cycle" name="billing_cycle" class="form-input" required>
                            <option value="yearly" @selected(old('billing_cycle', $license->billing_cycle) === 'yearly')>Yearly</option>
                            <option value="monthly" @selected(old('billing_cycle', $license->billing_cycle) === 'monthly')>Monthly</option>
                            <option value="quarterly" @selected(old('billing_cycle', $license->billing_cycle) === 'quarterly')>Quarterly</option>
                            <option value="one_time" @selected(old('billing_cycle', $license->billing_cycle) === 'one_time')>One Time</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Notes --}}
            <div class="card mb-6">
                <h3 class="text-sm font-semibold uppercase tracking-wider mb-4" style="color: var(--color-text-secondary);">Additional Notes</h3>
                <textarea id="notes" name="notes" rows="3" class="form-input">{{ old('notes', $license->notes) }}</textarea>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-between">
                <div x-data="{ confirmDelete: false }">
                    <button type="button" class="btn btn-danger text-sm" x-show="!confirmDelete" @click="confirmDelete = true">🗑 Delete License</button>
                    <div x-show="confirmDelete" class="flex items-center gap-2" x-transition>
                        <span class="text-sm" style="color: var(--color-status-danger);">Yakin hapus?</span>
                        <form method="POST" action="{{ route('licenses.destroy', $license) }}" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger text-sm">Ya, Hapus</button>
                        </form>
                        <button type="button" class="btn btn-secondary text-sm" @click="confirmDelete = false">Batal</button>
                    </div>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('licenses.show', $license) }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary" :disabled="loading">
                        <span x-text="loading ? 'Saving...' : '💾 Update License'">💾 Update License</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

</x-app-layout>
