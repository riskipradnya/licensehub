<x-app-layout title="Vendor Detail" :breadcrumbs="[['label' => 'Vendor Management', 'url' => route('vendors.index')], ['label' => $vendor->name]]">
    <div class="max-w-4xl mx-auto">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-xl flex items-center justify-center text-white text-xl font-bold shrink-0" style="background: {{ $vendor->color }};">{{ $vendor->initial }}</div>
                <div>
                    <h2 class="text-xl font-bold" style="color: var(--color-text-primary);">{{ $vendor->name }}</h2>
                    <p class="text-sm" style="color: var(--color-text-secondary);">{{ $vendor->email ?? '—' }}</p>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('vendors.index') }}" class="btn btn-secondary text-sm">← Back</a>
                <a href="{{ route('vendors.edit', $vendor) }}" class="btn btn-secondary text-sm">✏️ Edit Vendor</a>
            </div>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                {{-- Contact Info --}}
                <div class="card">
                    <h3 class="text-sm font-semibold uppercase tracking-wider mb-4" style="color: var(--color-text-secondary);">Contact Information</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div><p class="text-xs" style="color: var(--color-text-secondary);">Contact Person</p><p class="text-sm font-medium">{{ $vendor->contact_person ?? '—' }}</p></div>
                        <div><p class="text-xs" style="color: var(--color-text-secondary);">Email</p><p class="text-sm font-medium">{{ $vendor->email ?? '—' }}</p></div>
                        <div><p class="text-xs" style="color: var(--color-text-secondary);">Phone</p><p class="text-sm font-medium">{{ $vendor->phone ?? '—' }}</p></div>
                        <div><p class="text-xs" style="color: var(--color-text-secondary);">Website</p>
                            @if($vendor->website)
                                <a href="{{ $vendor->website }}" target="_blank" class="text-sm font-medium hover:underline" style="color: var(--color-primary);">{{ $vendor->website }}</a>
                            @else
                                <p class="text-sm font-medium">—</p>
                            @endif
                        </div>
                        <div class="col-span-2"><p class="text-xs" style="color: var(--color-text-secondary);">Address</p><p class="text-sm font-medium">{{ $vendor->address ?? '—' }}</p></div>
                    </div>
                </div>
                {{-- Associated Licenses --}}
                <div class="card p-0 overflow-hidden">
                    <div class="px-6 py-4" style="border-bottom: 1px solid var(--color-border);">
                        <h3 class="text-sm font-semibold uppercase tracking-wider" style="color: var(--color-text-secondary);">Associated Licenses ({{ $stats['total'] }})</h3>
                    </div>
                    @if($vendor->licenses->isEmpty())
                        <div class="px-6 py-8 text-center">
                            <p class="text-sm" style="color: var(--color-text-secondary);">Belum ada lisensi terkait vendor ini.</p>
                        </div>
                    @else
                        <table class="data-table">
                            <thead><tr><th>License</th><th>Category</th><th>Expiry</th><th>Status</th></tr></thead>
                            <tbody>
                                @foreach($vendor->licenses as $license)
                                <tr class="cursor-pointer hover:bg-gray-50" onclick="window.location='/licenses/{{ $license->id }}'">
                                    <td class="font-medium">{{ $license->name }}</td>
                                    <td>{{ $license->category->name ?? '—' }}</td>
                                    <td>{{ $license->expiry_date ? $license->expiry_date->format('d M Y') : '—' }}</td>
                                    <td>
                                        <x-status-badge :status="$license->status" :label="$license->status === 'expiring' ? 'H-'.$license->days_until_expiry : null" size="sm" />
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>

                {{-- Notes --}}
                @if($vendor->notes)
                <div class="card">
                    <h3 class="text-sm font-semibold uppercase tracking-wider mb-3" style="color: var(--color-text-secondary);">Notes</h3>
                    <p class="text-sm" style="color: var(--color-text-primary);">{{ $vendor->notes }}</p>
                </div>
                @endif
            </div>
            {{-- Sidebar --}}
            <div class="space-y-6">
                <div class="card text-center">
                    <p class="text-3xl font-bold" style="color: var(--color-primary);">{{ $stats['total'] }}</p>
                    <p class="text-xs uppercase mt-1" style="color: var(--color-text-secondary);">Total Licenses</p>
                    <hr class="my-3" style="border-color: var(--color-border);">
                    <p class="text-lg font-bold" style="color: var(--color-status-active);">{{ $stats['active'] }}</p>
                    <p class="text-xs" style="color: var(--color-text-secondary);">Active</p>
                    <p class="text-lg font-bold mt-2" style="color: var(--color-status-warning);">{{ $stats['expiring'] }}</p>
                    <p class="text-xs" style="color: var(--color-text-secondary);">Expiring Soon</p>
                    @if($stats['expired'] > 0)
                    <p class="text-lg font-bold mt-2" style="color: var(--color-status-danger);">{{ $stats['expired'] }}</p>
                    <p class="text-xs" style="color: var(--color-text-secondary);">Expired</p>
                    @endif
                </div>
                <div class="card">
                    <h3 class="text-sm font-semibold uppercase tracking-wider mb-3" style="color: var(--color-text-secondary);">SLA</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between"><span style="color: var(--color-text-secondary);">Response</span><span class="font-medium">{{ $vendor->sla_response_label }}</span></div>
                        <div class="flex justify-between"><span style="color: var(--color-text-secondary);">Support</span><span class="font-medium">{{ $vendor->sla_hours_label }}</span></div>
                        <div class="flex justify-between"><span style="color: var(--color-text-secondary);">Status</span><x-status-badge status="active" size="sm" /></div>
                    </div>
                </div>
                @if($vendor->bank_name || $vendor->bank_account_number)
                <div class="card">
                    <h3 class="text-sm font-semibold uppercase tracking-wider mb-3" style="color: var(--color-text-secondary);">Bank Info</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between"><span style="color: var(--color-text-secondary);">Bank</span><span class="font-medium">{{ $vendor->bank_name ?? '—' }}</span></div>
                        <div class="flex justify-between"><span style="color: var(--color-text-secondary);">No. Rek</span><span class="font-medium font-mono">{{ $vendor->bank_account_number ?? '—' }}</span></div>
                    </div>
                </div>
                @endif
                <div class="card">
                    <h3 class="text-sm font-semibold uppercase tracking-wider mb-3" style="color: var(--color-text-secondary);">Info</h3>
                    <div class="space-y-2 text-xs" style="color: var(--color-text-secondary);">
                        <div class="flex justify-between"><span>Ditambahkan</span><span class="font-medium" style="color: var(--color-text-primary);">{{ $vendor->created_at->format('d M Y') }}</span></div>
                        <div class="flex justify-between"><span>Terakhir diubah</span><span class="font-medium" style="color: var(--color-text-primary);">{{ $vendor->updated_at->diffForHumans() }}</span></div>
                    </div>
                </div>
            </div>
        </div>
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
