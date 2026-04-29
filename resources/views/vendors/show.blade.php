<x-app-layout title="Vendor Detail" :breadcrumbs="[['label' => 'Vendor Management', 'url' => '/vendors'], ['label' => 'Microsoft']]">
    <div class="max-w-4xl mx-auto">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-xl flex items-center justify-center text-white text-xl font-bold shrink-0" style="background: #0078d4;">M</div>
                <div>
                    <h2 class="text-xl font-bold" style="color: var(--color-text-primary);">Microsoft</h2>
                    <p class="text-sm" style="color: var(--color-text-secondary);">support@microsoft.com</p>
                </div>
            </div>
            <a href="/vendors/1/edit" class="btn btn-secondary text-sm">✏️ Edit Vendor</a>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                {{-- Contact Info --}}
                <div class="card">
                    <h3 class="text-sm font-semibold uppercase tracking-wider mb-4" style="color: var(--color-text-secondary);">Contact Information</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div><p class="text-xs" style="color: var(--color-text-secondary);">Email</p><p class="text-sm font-medium">support@microsoft.com</p></div>
                        <div><p class="text-xs" style="color: var(--color-text-secondary);">Phone</p><p class="text-sm font-medium">+1-800-642-7676</p></div>
                        <div class="col-span-2"><p class="text-xs" style="color: var(--color-text-secondary);">Address</p><p class="text-sm font-medium">One Microsoft Way, Redmond, WA 98052</p></div>
                    </div>
                </div>
                {{-- Associated Licenses --}}
                <div class="card p-0 overflow-hidden">
                    <div class="px-6 py-4" style="border-bottom: 1px solid var(--color-border);"><h3 class="text-sm font-semibold uppercase tracking-wider" style="color: var(--color-text-secondary);">Associated Licenses (24)</h3></div>
                    <table class="data-table">
                        <thead><tr><th>License</th><th>Category</th><th>Expiry</th><th>Status</th></tr></thead>
                        <tbody>
                            <tr><td class="font-medium">Windows Server 2022</td><td>OS</td><td>15 Dec 2027</td><td><x-status-badge status="active" size="sm" /></td></tr>
                            <tr><td class="font-medium">Microsoft 365 Business</td><td>Software</td><td>28 Apr 2026</td><td><x-status-badge status="expiring" label="H-14" size="sm" /></td></tr>
                            <tr><td class="font-medium">Windows 11 Pro</td><td>OS</td><td>—</td><td><x-status-badge status="active" label="Perpetual" size="sm" /></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
            {{-- Sidebar --}}
            <div class="space-y-6">
                <div class="card text-center">
                    <p class="text-3xl font-bold" style="color: var(--color-primary);">24</p>
                    <p class="text-xs uppercase mt-1" style="color: var(--color-text-secondary);">Total Licenses</p>
                    <hr class="my-3" style="border-color: var(--color-border);">
                    <p class="text-lg font-bold" style="color: var(--color-status-active);">22</p>
                    <p class="text-xs" style="color: var(--color-text-secondary);">Active</p>
                    <p class="text-lg font-bold mt-2" style="color: var(--color-status-warning);">2</p>
                    <p class="text-xs" style="color: var(--color-text-secondary);">Expiring Soon</p>
                </div>
                <div class="card">
                    <h3 class="text-sm font-semibold uppercase tracking-wider mb-3" style="color: var(--color-text-secondary);">SLA</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between"><span style="color: var(--color-text-secondary);">Response</span><span class="font-medium">24 Hours</span></div>
                        <div class="flex justify-between"><span style="color: var(--color-text-secondary);">Support</span><span class="font-medium">24/7</span></div>
                        <div class="flex justify-between"><span style="color: var(--color-text-secondary);">Status</span><x-status-badge status="active" size="sm" /></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
