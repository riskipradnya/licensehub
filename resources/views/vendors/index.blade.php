<x-app-layout title="Vendor Management" :breadcrumbs="[['label' => 'IT Department', 'url' => '#'], ['label' => 'Vendor List']]">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold" style="color: var(--color-text-primary);">Vendor Management</h2>
            <p class="text-sm mt-1" style="color: var(--color-text-secondary);">Kelola data vendor dan kontak support</p>
        </div>
        <a href="/vendors/create" class="btn btn-primary" id="add-vendor-btn">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Vendor
        </a>
    </div>

    {{-- SEARCH --}}
    <div class="mb-6">
        <div class="relative max-w-md">
            <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2" style="color: var(--color-text-secondary);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" placeholder="Cari vendor..." class="form-input pl-10" id="vendor-search">
        </div>
    </div>

    {{-- VENDOR CARDS GRID --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @php
        $vendors = [
            ['name' => 'Microsoft', 'email' => 'support@microsoft.com', 'phone' => '+1-800-642-7676', 'licenses' => 24, 'sla' => '24h', 'initial' => 'M', 'color' => '#0078d4'],
            ['name' => 'Oracle Corp', 'email' => 'support@oracle.com', 'phone' => '+1-800-672-2531', 'licenses' => 8, 'sla' => '48h', 'initial' => 'O', 'color' => '#f80000'],
            ['name' => 'Adobe Inc.', 'email' => 'support@adobe.com', 'phone' => '+1-800-833-6687', 'licenses' => 12, 'sla' => '24h', 'initial' => 'A', 'color' => '#ff0000'],
            ['name' => 'Kaspersky Lab', 'email' => 'support@kaspersky.com', 'phone' => '+7-495-797-8700', 'licenses' => 5, 'sla' => '72h', 'initial' => 'K', 'color' => '#006d5c'],
            ['name' => 'VHP', 'email' => 'info@vhp.co.id', 'phone' => '+62-21-5555-1234', 'licenses' => 3, 'sla' => '48h', 'initial' => 'V', 'color' => '#ff6600'],
            ['name' => 'VMware', 'email' => 'support@vmware.com', 'phone' => '+1-877-486-9273', 'licenses' => 6, 'sla' => '24h', 'initial' => 'V', 'color' => '#696566'],
        ];
        @endphp

        @foreach($vendors as $i => $v)
        <div class="card hover:shadow-lg transition-shadow cursor-pointer group" onclick="window.location='/vendors/{{ $i + 1 }}'">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white text-lg font-bold shrink-0"
                     style="background: {{ $v['color'] }};">
                    {{ $v['initial'] }}
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-sm font-semibold group-hover:text-indigo-600 transition" style="color: var(--color-text-primary);">{{ $v['name'] }}</h3>
                    <p class="text-xs mt-0.5" style="color: var(--color-text-secondary);">{{ $v['email'] }}</p>
                </div>
            </div>
            <div class="mt-4 pt-4 grid grid-cols-3 gap-2 text-center" style="border-top: 1px solid var(--color-border);">
                <div>
                    <p class="text-lg font-bold" style="color: var(--color-primary);">{{ $v['licenses'] }}</p>
                    <p class="text-[10px] uppercase" style="color: var(--color-text-secondary);">Licenses</p>
                </div>
                <div>
                    <p class="text-sm font-semibold" style="color: var(--color-text-primary);">{{ $v['sla'] }}</p>
                    <p class="text-[10px] uppercase" style="color: var(--color-text-secondary);">SLA</p>
                </div>
                <div>
                    <x-status-badge status="active" label="Active" size="sm" />
                </div>
            </div>
        </div>
        @endforeach
    </div>

</x-app-layout>
