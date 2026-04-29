<x-app-layout title="Add Vendor" :breadcrumbs="[['label' => 'Vendor Management', 'url' => '/vendors'], ['label' => 'Add Vendor']]">
    <div class="max-w-2xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold" style="color: var(--color-text-primary);">Add New Vendor</h2>
            <a href="/vendors" class="btn btn-secondary text-sm">← Back</a>
        </div>
        <form method="POST" action="/vendors" enctype="multipart/form-data" x-data="{ loading: false }" @submit="loading = true" id="create-vendor-form">
            @csrf
            <div class="card mb-6">
                <h3 class="text-sm font-semibold uppercase tracking-wider mb-4" style="color: var(--color-text-secondary);">Vendor Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2"><label class="form-label">Vendor Name *</label><input type="text" name="name" class="form-input" placeholder="e.g. Microsoft" required></div>
                    <div class="md:col-span-2"><label class="form-label">Address</label><textarea name="address" rows="2" class="form-input" placeholder="Alamat lengkap vendor"></textarea></div>
                    <div><label class="form-label">Email Support *</label><input type="email" name="email" class="form-input" placeholder="support@vendor.com" required></div>
                    <div><label class="form-label">Phone</label><input type="tel" name="phone" class="form-input" placeholder="+62-xxx-xxxx"></div>
                </div>
            </div>
            <div class="card mb-6">
                <h3 class="text-sm font-semibold uppercase tracking-wider mb-4" style="color: var(--color-text-secondary);">SLA Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div><label class="form-label">Response Time</label><select name="sla_response" class="form-input"><option value="24h">24 Hours</option><option value="48h">48 Hours</option><option value="72h">72 Hours</option></select></div>
                    <div><label class="form-label">Support Hours</label><select name="sla_hours" class="form-input"><option value="24/7">24/7</option><option value="business">Business Hours (9-17)</option></select></div>
                </div>
            </div>
            <div class="card mb-6">
                <h3 class="text-sm font-semibold uppercase tracking-wider mb-4" style="color: var(--color-text-secondary);">Notes</h3>
                <textarea name="notes" rows="3" class="form-input" placeholder="Catatan tambahan..."></textarea>
            </div>
            <div class="flex justify-end gap-3">
                <a href="/vendors" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary" :disabled="loading"><span x-text="loading ? 'Saving...' : '💾 Save Vendor'">💾 Save Vendor</span></button>
            </div>
        </form>
    </div>
</x-app-layout>
