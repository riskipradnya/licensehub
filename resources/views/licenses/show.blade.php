<x-app-layout title="License Detail" :breadcrumbs="[['label' => 'License Management', 'url' => '/licenses'], ['label' => 'Microsoft 365 Business']]">

    <div class="max-w-4xl mx-auto">
        {{-- HEADER --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0" style="background: var(--color-primary-50);">
                    <svg class="w-6 h-6" style="color: var(--color-primary);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold" style="color: var(--color-text-primary);">Microsoft 365 Business</h2>
                    <div class="flex items-center gap-2 mt-1">
                        <x-status-badge status="expiring" label="H-14 — Expiring Soon" />
                    </div>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="/licenses/1/edit" class="btn btn-secondary text-sm">✏️ Edit</a>
                <a href="/payments/process/1" class="btn btn-primary text-sm">💳 Renew Now</a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- MAIN INFO (2/3) --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- License Details --}}
                <div class="card">
                    <h3 class="text-sm font-semibold uppercase tracking-wider mb-4" style="color: var(--color-text-secondary);">License Details</h3>
                    <div class="grid grid-cols-2 gap-y-4 gap-x-6">
                        <div><p class="text-xs mb-1" style="color: var(--color-text-secondary);">Vendor</p><p class="text-sm font-medium">Microsoft</p></div>
                        <div><p class="text-xs mb-1" style="color: var(--color-text-secondary);">Category</p><p class="text-sm font-medium">Software</p></div>
                        <div><p class="text-xs mb-1" style="color: var(--color-text-secondary);">License Type</p><p class="text-sm font-medium">Subscription</p></div>
                        <div><p class="text-xs mb-1" style="color: var(--color-text-secondary);">Serial Key</p><p class="text-sm font-medium font-mono">ABCD-EFGH-IJKL-MNOP</p></div>
                        <div><p class="text-xs mb-1" style="color: var(--color-text-secondary);">Start Date</p><p class="text-sm font-medium">28 Apr 2025</p></div>
                        <div><p class="text-xs mb-1" style="color: var(--color-text-secondary);">Expiry Date</p><p class="text-sm font-medium" style="color: var(--color-status-warning);">28 Apr 2026</p></div>
                        <div><p class="text-xs mb-1" style="color: var(--color-text-secondary);">Seats / Users</p><p class="text-sm font-medium">50 users</p></div>
                        <div><p class="text-xs mb-1" style="color: var(--color-text-secondary);">Cost</p><p class="text-sm font-bold" style="color: var(--color-primary);">Rp 12.000.000</p></div>
                    </div>
                </div>

                {{-- Documents --}}
                <div class="card">
                    <h3 class="text-sm font-semibold uppercase tracking-wider mb-4" style="color: var(--color-text-secondary);">Attached Documents</h3>
                    <div class="space-y-2">
                        <div class="flex items-center gap-3 p-3 rounded-lg" style="background: var(--color-content-bg); border: 1px solid var(--color-border);">
                            <svg class="w-5 h-5 shrink-0" style="color: var(--color-primary);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                            <div class="flex-1"><p class="text-sm font-medium">Kontrak_MS365_2025.pdf</p><p class="text-xs" style="color: var(--color-text-secondary);">2.1 MB • Uploaded 28 Apr 2025</p></div>
                            <a href="#" class="btn btn-ghost text-xs">👁 View</a>
                            <a href="#" class="btn btn-ghost text-xs">⬇ Download</a>
                        </div>
                        <div class="flex items-center gap-3 p-3 rounded-lg" style="background: var(--color-content-bg); border: 1px solid var(--color-border);">
                            <svg class="w-5 h-5 shrink-0" style="color: var(--color-primary);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                            <div class="flex-1"><p class="text-sm font-medium">Invoice_MS365_2025.pdf</p><p class="text-xs" style="color: var(--color-text-secondary);">1.4 MB • Uploaded 28 Apr 2025</p></div>
                            <a href="#" class="btn btn-ghost text-xs">👁 View</a>
                            <a href="#" class="btn btn-ghost text-xs">⬇ Download</a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SIDEBAR INFO (1/3) --}}
            <div class="space-y-6">
                {{-- Countdown --}}
                <div class="card text-center" style="border-left: 4px solid var(--color-status-warning);">
                    <p class="text-xs font-semibold uppercase tracking-wider mb-2" style="color: var(--color-status-warning);">Time Remaining</p>
                    <p class="text-4xl font-bold" style="color: var(--color-text-primary);">14</p>
                    <p class="text-sm" style="color: var(--color-text-secondary);">hari tersisa</p>
                    <a href="/payments/process/1" class="btn btn-primary w-full mt-4 text-sm">💳 Renew Now</a>
                </div>

                {{-- Payment History --}}
                <div class="card">
                    <h3 class="text-sm font-semibold uppercase tracking-wider mb-3" style="color: var(--color-text-secondary);">Payment History</h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <div><p class="text-sm font-medium">28 Apr 2025</p><p class="text-xs" style="color: var(--color-text-secondary);">Rp 12.000.000</p></div>
                            <x-status-badge status="paid" size="sm" />
                        </div>
                        <div class="flex items-center justify-between">
                            <div><p class="text-sm font-medium">28 Apr 2024</p><p class="text-xs" style="color: var(--color-text-secondary);">Rp 10.800.000</p></div>
                            <x-status-badge status="paid" size="sm" />
                        </div>
                    </div>
                </div>

                {{-- Change Log --}}
                <div class="card">
                    <h3 class="text-sm font-semibold uppercase tracking-wider mb-3" style="color: var(--color-text-secondary);">Recent Changes</h3>
                    <div class="space-y-3 text-xs">
                        <div class="flex gap-2"><span style="color: var(--color-text-secondary);">Today</span><span>Admin updated cost</span></div>
                        <div class="flex gap-2"><span style="color: var(--color-text-secondary);">3d ago</span><span>IT Staff uploaded document</span></div>
                        <div class="flex gap-2"><span style="color: var(--color-text-secondary);">1w ago</span><span>License created by Admin</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
