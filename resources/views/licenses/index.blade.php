<x-app-layout title="License Management" :breadcrumbs="[['label' => 'IT Department', 'url' => '#'], ['label' => 'License List']]">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold" style="color: var(--color-text-primary);">License Management</h2>
            <p class="text-sm mt-1" style="color: var(--color-text-secondary);">Kelola semua lisensi perangkat lunak perusahaan</p>
        </div>
        <a href="/licenses/create" class="btn btn-primary" id="add-license-btn">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add License
        </a>
    </div>

    {{-- FILTER BAR --}}
    <div class="card mb-6">
        <div class="flex flex-col md:flex-row gap-3" x-data="{ category: '', status: '', search: '' }">
            <div class="relative flex-1">
                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2" style="color: var(--color-text-secondary);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" x-model="search" placeholder="Cari nama lisensi, vendor..." class="form-input pl-10" id="license-search">
            </div>
            <select x-model="category" class="form-input w-full md:w-48" id="filter-category">
                <option value="">All Categories</option>
                <option value="os">OS</option>
                <option value="software">Software</option>
                <option value="antivirus">Antivirus</option>
                <option value="security">Security</option>
            </select>
            <select x-model="status" class="form-input w-full md:w-40" id="filter-status">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="expiring">Expiring Soon</option>
                <option value="expired">Expired</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>
    </div>

    {{-- DATA TABLE --}}
    <div class="card p-0 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table" id="licenses-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>License Name</th>
                        <th>Vendor</th>
                        <th>Category</th>
                        <th>Type</th>
                        <th>Expiry Date</th>
                        <th>Days Left</th>
                        <th>Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $licenses = [
                        ['name' => 'Windows Server 2022', 'vendor' => 'Microsoft', 'cat' => 'OS', 'catColor' => 'active', 'type' => 'Perpetual', 'exp' => '15 Dec 2027', 'days' => 598, 'status' => 'active'],
                        ['name' => 'Microsoft 365 Business', 'vendor' => 'Microsoft', 'cat' => 'Software', 'catColor' => 'info', 'type' => 'Subscription', 'exp' => '28 Apr 2026', 'days' => 14, 'status' => 'expiring'],
                        ['name' => 'Adobe Creative Cloud', 'vendor' => 'Adobe Inc.', 'cat' => 'Software', 'catColor' => 'info', 'type' => 'Subscription', 'exp' => '10 Apr 2027', 'days' => 361, 'status' => 'active'],
                        ['name' => 'Oracle Database Enterprise', 'vendor' => 'Oracle Corp', 'cat' => 'Software', 'catColor' => 'info', 'type' => 'Subscription', 'exp' => '21 Apr 2026', 'days' => 7, 'status' => 'expiring'],
                        ['name' => 'Kaspersky Endpoint Security', 'vendor' => 'Kaspersky Lab', 'cat' => 'Security', 'catColor' => 'danger', 'type' => 'Subscription', 'exp' => '15 Apr 2026', 'days' => 1, 'status' => 'expired'],
                        ['name' => 'ESET NOD32 Antivirus', 'vendor' => 'VHP', 'cat' => 'Antivirus', 'catColor' => 'warning', 'type' => 'Subscription', 'exp' => '10 May 2026', 'days' => 26, 'status' => 'expiring'],
                        ['name' => 'VMware vSphere', 'vendor' => 'VMware', 'cat' => 'Software', 'catColor' => 'info', 'type' => 'Perpetual', 'exp' => '01 Mar 2028', 'days' => 674, 'status' => 'active'],
                        ['name' => 'Red Hat Enterprise Linux', 'vendor' => 'Red Hat', 'cat' => 'OS', 'catColor' => 'active', 'type' => 'Subscription', 'exp' => '20 Jul 2026', 'days' => 97, 'status' => 'active'],
                        ['name' => 'Fortinet FortiGate', 'vendor' => 'Fortinet', 'cat' => 'Security', 'catColor' => 'danger', 'type' => 'Subscription', 'exp' => '01 Jan 2026', 'days' => 0, 'status' => 'expired'],
                        ['name' => 'Slack Enterprise', 'vendor' => 'Salesforce', 'cat' => 'Software', 'catColor' => 'info', 'type' => 'Subscription', 'exp' => '15 Sep 2026', 'days' => 154, 'status' => 'active'],
                    ];
                    @endphp

                    @foreach($licenses as $i => $lic)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td class="font-medium">{{ $lic['name'] }}</td>
                        <td>{{ $lic['vendor'] }}</td>
                        <td><span class="text-xs px-2 py-0.5 rounded-md" style="background: var(--color-status-{{ $lic['catColor'] }}-bg); color: var(--color-status-{{ $lic['catColor'] }});">{{ $lic['cat'] }}</span></td>
                        <td><span class="text-xs">{{ $lic['type'] }}</span></td>
                        <td>{{ $lic['exp'] }}</td>
                        <td>
                            @if($lic['days'] <= 1)
                                <span class="font-semibold" style="color: var(--color-status-danger);">{{ $lic['days'] }} hari</span>
                            @elseif($lic['days'] <= 30)
                                <span class="font-semibold" style="color: var(--color-status-warning);">{{ $lic['days'] }} hari</span>
                            @else
                                <span>{{ $lic['days'] }} hari</span>
                            @endif
                        </td>
                        <td><x-status-badge :status="$lic['status']" size="sm" /></td>
                        <td class="text-center">
                            <div class="flex items-center justify-center gap-1">
                                <a href="/licenses/{{ $i + 1 }}" class="btn-ghost p-1.5 rounded-lg" title="View Detail">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                <a href="/licenses/{{ $i + 1 }}/edit" class="btn-ghost p-1.5 rounded-lg" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{-- PAGINATION --}}
        <div class="px-6 py-3 flex items-center justify-between" style="border-top: 1px solid var(--color-border);">
            <span class="text-xs" style="color: var(--color-text-secondary);">Showing 1-10 of 120 licenses</span>
            <div class="pagination">
                <span class="pagination-item">◀</span>
                <span class="pagination-item active">1</span>
                <span class="pagination-item">2</span>
                <span class="pagination-item">3</span>
                <span class="pagination-item">...</span>
                <span class="pagination-item">12</span>
                <span class="pagination-item">▶</span>
            </div>
        </div>
    </div>

</x-app-layout>
