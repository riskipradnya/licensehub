<x-app-layout title="License Management" :breadcrumbs="[['label' => 'IT Department', 'url' => '#'], ['label' => 'License List']]">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold" style="color: var(--color-text-primary);">License Management</h2>
            <p class="text-sm mt-1" style="color: var(--color-text-secondary);">Kelola semua lisensi perangkat lunak perusahaan</p>
        </div>
        <a href="{{ route('licenses.create') }}" class="btn btn-primary" id="add-license-btn">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add License
        </a>
    </div>

    {{-- FILTER BAR --}}
    <div class="card mb-6">
        <form method="GET" action="{{ route('licenses.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 w-full" id="license-filter-form">
            <div class="relative md:col-span-2">
                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2" style="color: var(--color-text-secondary);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama lisensi, vendor..." class="form-input w-full !pl-10" id="license-search">
            </div>
            <div class="md:col-span-1">
                <select name="category" class="form-input w-full" id="filter-category" onchange="this.form.submit()">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" @selected(request('category') == $cat->id)>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-1">
                <select name="status" class="form-input w-full" id="filter-status" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="active" @selected(request('status') === 'active')>Active</option>
                    <option value="expiring" @selected(request('status') === 'expiring')>Expiring Soon</option>
                    <option value="expired" @selected(request('status') === 'expired')>Expired</option>
                    <option value="cancelled" @selected(request('status') === 'cancelled')>Cancelled</option>
                </select>
            </div>
        </form>
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
                    @forelse($licenses as $i => $license)
                    <tr>
                        <td>{{ $licenses->firstItem() + $i }}</td>
                        <td class="font-medium">{{ $license->name }}</td>
                        <td>{{ $license->vendor->name ?? '—' }}</td>
                        <td>
                            <span class="w-32 inline-block text-center truncate text-xs px-2 py-1 rounded-md"
                                  style="background: var(--color-status-{{ $license->category_color }}-bg); color: var(--color-status-{{ $license->category_color }});">
                                {{ $license->category->name ?? '—' }}
                            </span>
                        </td>
                        <td><span class="text-xs capitalize">{{ $license->type }}</span></td>
                        <td>{{ $license->expiry_date ? $license->expiry_date->format('d M Y') : '— Perpetual' }}</td>
                        <td>
                            @if($license->days_until_expiry === null)
                                <span class="text-xs" style="color: var(--color-text-secondary);">∞</span>
                            @elseif($license->days_until_expiry <= 0)
                                <span class="font-semibold" style="color: var(--color-status-danger);">{{ abs($license->days_until_expiry) }} hari lalu</span>
                            @elseif($license->days_until_expiry <= 30)
                                <span class="font-semibold" style="color: var(--color-status-warning);">{{ $license->days_until_expiry }} hari</span>
                            @else
                                <span>{{ $license->days_until_expiry }} hari</span>
                            @endif
                        </td>
                        <td><x-status-badge :status="$license->status" size="sm" /></td>
                        <td class="text-center">
                            <div class="flex items-center justify-center gap-1">
                                <a href="{{ route('licenses.show', $license) }}" class="btn-ghost p-1.5 rounded-lg" title="View Detail">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                <a href="{{ route('licenses.edit', $license) }}" class="btn-ghost p-1.5 rounded-lg" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-8">
                            <p class="text-sm" style="color: var(--color-text-secondary);">Tidak ada lisensi ditemukan.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{-- PAGINATION --}}
        @if($licenses->hasPages())
        <div class="px-6 py-3 flex items-center justify-between" style="border-top: 1px solid var(--color-border);">
            <span class="text-xs" style="color: var(--color-text-secondary);">
                Showing {{ $licenses->firstItem() }}–{{ $licenses->lastItem() }} of {{ $licenses->total() }} licenses
            </span>
            <div>
                {{ $licenses->links('pagination::tailwind') }}
            </div>
        </div>
        @endif
    </div>

    {{-- Flash toast --}}
    @if(session('success'))
    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            setTimeout(() => {
                window.dispatchEvent(new CustomEvent('toast', {
                    detail: { message: @json(session('success')), type: 'success' }
                }));
            }, 300);
        });
    </script>
    @endpush
    @endif

</x-app-layout>
