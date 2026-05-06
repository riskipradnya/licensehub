<x-app-layout title="Vendor Management" :breadcrumbs="[['label' => 'IT Department', 'url' => '#'], ['label' => 'Vendor List']]">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold" style="color: var(--color-text-primary);">Vendor Management</h2>
            <p class="text-sm mt-1" style="color: var(--color-text-secondary);">Kelola data vendor dan kontak support</p>
        </div>
        <a href="{{ route('vendors.create') }}" class="btn btn-primary" id="add-vendor-btn">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Vendor
        </a>
    </div>

    {{-- SEARCH --}}
    <div class="mb-6">
        <form method="GET" action="{{ route('vendors.index') }}">
            <div class="relative max-w-md">
                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2" style="color: var(--color-text-secondary);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari vendor..." class="form-input pl-10" id="vendor-search">
            </div>
        </form>
    </div>

    {{-- VENDOR CARDS GRID --}}
    @if($vendors->isEmpty())
        <x-empty-state title="Belum ada vendor" description="Tambahkan vendor pertama Anda untuk mulai mengelola lisensi." />
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($vendors as $vendor)
            <div class="card hover:shadow-lg transition-shadow cursor-pointer group" onclick="window.location='{{ route('vendors.show', $vendor) }}'">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white text-lg font-bold shrink-0"
                         style="background: {{ $vendor->color }};">
                        {{ $vendor->initial }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm font-semibold group-hover:text-indigo-600 transition" style="color: var(--color-text-primary);">{{ $vendor->name }}</h3>
                        <p class="text-xs mt-0.5" style="color: var(--color-text-secondary);">{{ $vendor->email ?? '—' }}</p>
                    </div>
                </div>
                <div class="mt-4 pt-4 grid grid-cols-3 gap-2 text-center" style="border-top: 1px solid var(--color-border);">
                    <div>
                        <p class="text-lg font-bold" style="color: var(--color-primary);">{{ $vendor->licenses_count }}</p>
                        <p class="text-[10px] uppercase" style="color: var(--color-text-secondary);">Licenses</p>
                    </div>
                    <div>
                        <p class="text-sm font-semibold" style="color: var(--color-text-primary);">{{ $vendor->sla_response ?? '—' }}</p>
                        <p class="text-[10px] uppercase" style="color: var(--color-text-secondary);">SLA</p>
                    </div>
                    <div>
                        <x-status-badge status="active" label="Active" size="sm" />
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif

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
