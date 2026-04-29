<x-app-layout title="Edit Vendor" :breadcrumbs="[['label' => 'Vendor Management', 'url' => '/vendors'], ['label' => 'Edit Vendor']]">
    <div class="max-w-2xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold" style="color: var(--color-text-primary);">Edit Vendor</h2>
            <a href="/vendors" class="btn btn-secondary text-sm">← Back</a>
        </div>
        <form method="POST" action="/vendors/1" x-data="{ loading: false }" @submit="loading = true" id="edit-vendor-form">
            @csrf @method('PUT')
            <div class="card mb-6">
                <h3 class="text-sm font-semibold uppercase tracking-wider mb-4" style="color: var(--color-text-secondary);">Vendor Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2"><label class="form-label">Vendor Name *</label><input type="text" name="name" class="form-input" value="Microsoft" required></div>
                    <div class="md:col-span-2"><label class="form-label">Address</label><textarea name="address" rows="2" class="form-input">One Microsoft Way, Redmond, WA</textarea></div>
                    <div><label class="form-label">Email Support *</label><input type="email" name="email" class="form-input" value="support@microsoft.com" required></div>
                    <div><label class="form-label">Phone</label><input type="tel" name="phone" class="form-input" value="+1-800-642-7676"></div>
                </div>
            </div>
            <div class="card mb-6">
                <h3 class="text-sm font-semibold uppercase tracking-wider mb-4" style="color: var(--color-text-secondary);">SLA Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div><label class="form-label">Response Time</label><select name="sla_response" class="form-input"><option value="24h" selected>24 Hours</option><option value="48h">48 Hours</option><option value="72h">72 Hours</option></select></div>
                    <div><label class="form-label">Support Hours</label><select name="sla_hours" class="form-input"><option value="24/7" selected>24/7</option><option value="business">Business Hours</option></select></div>
                </div>
            </div>
            <div class="flex items-center justify-between">
                <button type="button" class="btn btn-danger text-sm">🗑 Delete Vendor</button>
                <div class="flex gap-3">
                    <a href="/vendors" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary" :disabled="loading"><span x-text="loading ? 'Saving...' : '💾 Update Vendor'">💾 Update Vendor</span></button>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>
