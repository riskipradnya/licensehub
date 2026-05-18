<x-app-layout title="License Detail" :breadcrumbs="[['label' => 'License Management', 'url' => route('licenses.index')], ['label' => $license->name]]">

    <div class="max-w-4xl mx-auto">
        {{-- HEADER --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0" style="background: var(--color-primary-50);">
                    <svg class="w-6 h-6" style="color: var(--color-primary);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold" style="color: var(--color-text-primary);">{{ $license->name }}</h2>
                    <div class="flex items-center gap-2 mt-1">
                        @if($license->days_until_expiry !== null)
                            @if($license->days_until_expiry <= 0)
                                <x-status-badge status="expired" label="Expired {{ abs($license->days_until_expiry) }} hari lalu" />
                            @elseif($license->days_until_expiry <= 30)
                                <x-status-badge status="expiring" label="H-{{ $license->days_until_expiry }} — Expiring Soon" />
                            @else
                                <x-status-badge status="active" />
                            @endif
                        @else
                            <x-status-badge status="active" label="Perpetual" />
                        @endif
                    </div>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('licenses.index') }}" class="btn btn-secondary text-sm">← Back</a>
                <a href="{{ route('licenses.edit', $license) }}" class="btn btn-secondary text-sm">✏️ Edit</a>
                @if($license->status === 'expiring' || $license->status === 'expired')
                <a href="{{ route('payments.renew', $license) }}" class="btn btn-primary text-sm">💳 Renew Now</a>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- MAIN INFO (2/3) --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- License Details --}}
                <div class="card">
                    <h3 class="text-sm font-semibold uppercase tracking-wider mb-4" style="color: var(--color-text-secondary);">License Details</h3>
                    <div class="grid grid-cols-2 gap-y-4 gap-x-6">
                        <div><p class="text-xs mb-1" style="color: var(--color-text-secondary);">Vendor</p><p class="text-sm font-medium">
                            @if($license->vendor)
                                <a href="{{ route('vendors.show', $license->vendor) }}" class="hover:underline" style="color: var(--color-primary);">{{ $license->vendor->name }}</a>
                            @else
                                <span>—</span>
                            @endif
                        </p></div>
                        <div><p class="text-xs mb-1" style="color: var(--color-text-secondary);">Category</p><p class="text-sm font-medium">{{ $license->category->name ?? '—' }}</p></div>
                        <div><p class="text-xs mb-1" style="color: var(--color-text-secondary);">License Type</p><p class="text-sm font-medium capitalize">{{ $license->type }}</p></div>
                        <div><p class="text-xs mb-1" style="color: var(--color-text-secondary);">Serial Key</p>
                            <p class="text-sm font-medium font-mono">{{ $license->serial_key ?? '—' }}</p>
                        </div>
                        <div><p class="text-xs mb-1" style="color: var(--color-text-secondary);">Start Date</p><p class="text-sm font-medium">{{ $license->start_date->format('d M Y') }}</p></div>
                        <div><p class="text-xs mb-1" style="color: var(--color-text-secondary);">Expiry Date</p>
                            @if($license->expiry_date)
                                <p class="text-sm font-medium" style="color: var(--color-status-{{ $license->days_until_expiry <= 30 ? ($license->days_until_expiry <= 0 ? 'danger' : 'warning') : 'active' }});">
                                    {{ $license->expiry_date->format('d M Y') }}
                                </p>
                            @else
                                <p class="text-sm font-medium">— Perpetual</p>
                            @endif
                        </div>
                        <div><p class="text-xs mb-1" style="color: var(--color-text-secondary);">Seats / Users</p><p class="text-sm font-medium">{{ $license->seats ? $license->seats . ' users' : '—' }}</p></div>
                        <div><p class="text-xs mb-1" style="color: var(--color-text-secondary);">Cost</p><p class="text-sm font-bold" style="color: var(--color-primary);">{{ $license->formatted_cost }}</p></div>
                        <div><p class="text-xs mb-1" style="color: var(--color-text-secondary);">Billing Cycle</p><p class="text-sm font-medium capitalize">{{ str_replace('_', ' ', $license->billing_cycle) }}</p></div>
                        <div><p class="text-xs mb-1" style="color: var(--color-text-secondary);">Created By</p><p class="text-sm font-medium">{{ $license->creator->name ?? '—' }}</p></div>
                    </div>
                </div>

                {{-- Notes --}}
                @if($license->notes)
                <div class="card">
                    <h3 class="text-sm font-semibold uppercase tracking-wider mb-3" style="color: var(--color-text-secondary);">Notes</h3>
                    <p class="text-sm" style="color: var(--color-text-primary);">{{ $license->notes }}</p>
                </div>
                @endif

                {{-- Documents --}}
                <div class="card">
                    <h3 class="text-sm font-semibold uppercase tracking-wider mb-4" style="color: var(--color-text-secondary);">Attached Documents</h3>
                    @if($license->documents->isEmpty())
                        <p class="text-sm text-center py-4" style="color: var(--color-text-secondary);">Belum ada dokumen terlampir.</p>
                    @else
                        <div class="space-y-2">
                            @foreach($license->documents as $doc)
                            <div class="flex items-center gap-3 p-3 rounded-lg" style="background: var(--color-content-bg); border: 1px solid var(--color-border);">
                                <svg class="w-5 h-5 shrink-0" style="color: var(--color-primary);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                                <div class="flex-1">
                                    <p class="text-sm font-medium">{{ $doc->original_name }}</p>
                                    <p class="text-xs" style="color: var(--color-text-secondary);">{{ $doc->formatted_size }} • {{ $doc->created_at->format('d M Y') }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- SIDEBAR INFO (1/3) --}}
            <div class="space-y-6">
                {{-- Countdown --}}
                @if($license->expiry_date)
                <div class="card text-center" style="border-left: 4px solid var(--color-status-{{ $license->days_until_expiry <= 0 ? 'danger' : ($license->days_until_expiry <= 30 ? 'warning' : 'active') }});">
                    <p class="text-xs font-semibold uppercase tracking-wider mb-2"
                       style="color: var(--color-status-{{ $license->days_until_expiry <= 0 ? 'danger' : ($license->days_until_expiry <= 30 ? 'warning' : 'active') }});">
                        {{ $license->days_until_expiry <= 0 ? 'Expired' : 'Time Remaining' }}
                    </p>
                    <p class="text-4xl font-bold" style="color: var(--color-text-primary);">{{ abs($license->days_until_expiry) }}</p>
                    <p class="text-sm" style="color: var(--color-text-secondary);">{{ $license->days_until_expiry <= 0 ? 'hari yang lalu' : 'hari tersisa' }}</p>
                    @if($license->days_until_expiry > 0 && $license->days_until_expiry <= 30)
                    <a href="{{ route('payments.renew', $license) }}" class="btn btn-primary w-full mt-4 text-sm">💳 Renew Now</a>
                    @endif
                </div>
                @else
                <div class="card text-center" style="border-left: 4px solid var(--color-status-active);">
                    <p class="text-xs font-semibold uppercase tracking-wider mb-2" style="color: var(--color-status-active);">License Type</p>
                    <p class="text-2xl font-bold" style="color: var(--color-text-primary);">∞</p>
                    <p class="text-sm" style="color: var(--color-text-secondary);">Perpetual — No Expiry</p>
                </div>
                @endif

                {{-- Payment History --}}
                <div class="card">
                    <h3 class="text-sm font-semibold uppercase tracking-wider mb-3" style="color: var(--color-text-secondary);">Payment History</h3>
                    @if($license->payments->isEmpty())
                        <p class="text-xs text-center py-2" style="color: var(--color-text-secondary);">Belum ada riwayat pembayaran.</p>
                    @else
                        <div class="space-y-3">
                            @foreach($license->payments as $payment)
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium">{{ $payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') : '—' }}</p>
                                    <p class="text-xs" style="color: var(--color-text-secondary);">Rp {{ number_format($payment->amount, 0, ',', '.') }}</p>
                                </div>
                                <x-status-badge :status="$payment->status" size="sm" />
                            </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Change Log --}}
                <div class="card">
                    <h3 class="text-sm font-semibold uppercase tracking-wider mb-3" style="color: var(--color-text-secondary);">Recent Changes</h3>
                    @if($auditLogs->isEmpty())
                        <p class="text-xs text-center py-2" style="color: var(--color-text-secondary);">Belum ada perubahan.</p>
                    @else
                        <div class="space-y-3 text-xs">
                            @foreach($auditLogs as $log)
                            <div class="flex gap-2">
                                <span style="color: var(--color-text-secondary);">{{ $log->created_at->diffForHumans() }}</span>
                                <span>{{ $log->user?->name ?? 'System' }} {{ $log->action }}</span>
                            </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Info --}}
                <div class="card">
                    <h3 class="text-sm font-semibold uppercase tracking-wider mb-3" style="color: var(--color-text-secondary);">Info</h3>
                    <div class="space-y-2 text-xs" style="color: var(--color-text-secondary);">
                        <div class="flex justify-between"><span>Ditambahkan</span><span class="font-medium" style="color: var(--color-text-primary);">{{ $license->created_at->format('d M Y') }}</span></div>
                        <div class="flex justify-between"><span>Terakhir diubah</span><span class="font-medium" style="color: var(--color-text-primary);">{{ $license->updated_at->diffForHumans() }}</span></div>
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
