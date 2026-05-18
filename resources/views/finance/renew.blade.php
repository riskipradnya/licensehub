<x-app-layout title="Process Payment" :breadcrumbs="[['label' => 'Finance', 'url' => '#'], ['label' => 'Process Payment']]">

    <div class="max-w-3xl mx-auto">

        {{-- HEADER --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xl font-bold" style="color: var(--color-text-primary);">Disbursement ke Vendor</h2>
                <p class="text-sm mt-1" style="color: var(--color-text-secondary);">Transfer pembayaran lisensi ke rekening vendor</p>
            </div>
            <a href="{{ route('licenses.show', $license) }}" class="btn btn-secondary text-sm">← Back to License</a>
        </div>

        {{-- ERROR FLASH --}}
        @if(session('error'))
        <div class="card mb-6 p-4" style="border-left: 4px solid var(--color-status-danger); background: var(--color-status-danger-bg);">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 shrink-0" style="color: var(--color-status-danger);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                <p class="text-sm font-medium" style="color: var(--color-status-danger);">{{ session('error') }}</p>
            </div>
        </div>
        @endif

        {{-- LICENSE SUMMARY CARD --}}
        <div class="card mb-6" style="border-left: 4px solid var(--color-primary);">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0" style="background: var(--color-primary-50);">
                    <svg class="w-6 h-6" style="color: var(--color-primary);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-lg font-bold" style="color: var(--color-text-primary);">{{ $license->name }}</h3>
                    <p class="text-sm mt-0.5" style="color: var(--color-text-secondary);">{{ $license->vendor->name ?? '—' }}</p>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-4">
                        <div>
                            <p class="text-[10px] uppercase tracking-wider" style="color: var(--color-text-secondary);">Category</p>
                            <p class="text-sm font-medium">{{ $license->category->name ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase tracking-wider" style="color: var(--color-text-secondary);">Type</p>
                            <p class="text-sm font-medium capitalize">{{ $license->type }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase tracking-wider" style="color: var(--color-text-secondary);">Expiry Date</p>
                            <p class="text-sm font-semibold" style="color: var(--color-status-{{ ($license->days_until_expiry ?? 999) <= 30 ? (($license->days_until_expiry ?? 999) <= 0 ? 'danger' : 'warning') : 'active' }});">
                                {{ $license->expiry_date ? $license->expiry_date->format('d M Y') : 'Perpetual' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase tracking-wider" style="color: var(--color-text-secondary);">Days Left</p>
                            <p class="text-sm font-bold" style="color: var(--color-status-{{ ($license->days_until_expiry ?? 999) <= 7 ? 'danger' : 'warning' }});">
                                @if($license->days_until_expiry !== null)
                                    {{ $license->days_until_expiry <= 0 ? 'Expired' : $license->days_until_expiry . ' hari' }}
                                @else
                                    ∞
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- DISBURSEMENT DETAILS --}}
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

            {{-- DISBURSEMENT FORM (3/5) --}}
            <div class="lg:col-span-3">
                <form method="POST" action="{{ route('xendit.disburse') }}" class="card" id="disbursementForm">
                    @csrf
                    <input type="hidden" name="license_id" value="{{ $license->id }}">
                    <h3 class="text-sm font-semibold uppercase tracking-wider mb-4" style="color: var(--color-text-secondary);">Disbursement Information</h3>

                    <div class="space-y-4">
                        {{-- Vendor Bank Info --}}
                        @if($license->vendor && $license->vendor->bank_name && $license->vendor->bank_account_number)
                        <div class="p-4 rounded-xl" style="background: var(--color-status-info-bg); border: 1px solid var(--color-border);">
                            <div class="flex items-center gap-2 mb-2">
                                <svg class="w-4 h-4" style="color: var(--color-status-info);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                <p class="text-xs font-semibold uppercase" style="color: var(--color-status-info);">Rekening Tujuan Vendor</p>
                            </div>
                            <p class="text-sm font-semibold" style="color: var(--color-text-primary);">{{ \App\Http\Controllers\XenditController::BANK_CODES[$license->vendor->bank_name] ?? $license->vendor->bank_name }}</p>
                            <p class="text-lg font-mono font-bold mt-1" style="color: var(--color-text-primary);">{{ $license->vendor->bank_account_number }}</p>
                            <p class="text-xs mt-1" style="color: var(--color-text-secondary);">a.n. {{ $license->vendor->name }}</p>
                        </div>
                        @else
                        <div class="p-4 rounded-xl" style="background: var(--color-status-danger-bg); border: 1px solid var(--color-status-danger);">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5" style="color: var(--color-status-danger);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                                <div>
                                    <p class="text-sm font-semibold" style="color: var(--color-status-danger);">Rekening vendor belum diisi!</p>
                                    <p class="text-xs mt-0.5" style="color: var(--color-text-secondary);">Silakan update data bank vendor terlebih dahulu.</p>
                                </div>
                            </div>
                            @if($license->vendor)
                                <a href="{{ route('vendors.edit', $license->vendor) }}" class="btn btn-secondary text-xs mt-3">✏️ Edit Vendor</a>
                            @else
                                <p class="text-xs text-red-500 mt-2">Lisensi ini tidak terhubung dengan vendor apapun.</p>
                            @endif
                        </div>
                        @endif

                        {{-- Amount --}}
                        <div>
                            <label class="form-label">Jumlah Transfer (Rp) <span style="color: var(--color-status-danger);">*</span></label>
                            <input type="number" name="amount" class="form-input text-lg font-bold"
                                   value="{{ old('amount', (int) $license->cost) }}" required min="1">
                            <p class="text-xs mt-1" style="color: var(--color-text-secondary);">Biaya lisensi berdasarkan data terakhir</p>
                        </div>

                        {{-- Notes --}}
                        <div>
                            <label class="form-label">Catatan</label>
                            <textarea name="notes" class="form-input" rows="2" placeholder="Catatan disbursement...">{{ old('notes', 'Pembayaran perpanjangan ' . $license->name) }}</textarea>
                        </div>

                        {{-- Method Info --}}
                        <div class="p-3 rounded-lg flex items-center gap-3" style="background: var(--color-content-bg); border: 1px solid var(--color-border);">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0" style="background: var(--color-primary-50);">
                                <svg class="w-5 h-5" style="color: var(--color-primary);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold">Xendit — Disbursement API</p>
                                <p class="text-[10px]" style="color: var(--color-text-secondary);">Dana akan ditransfer langsung ke rekening vendor via Xendit Payout</p>
                            </div>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="mt-6 pt-4" style="border-top: 1px solid var(--color-border);">
                        @if($license->vendor && $license->vendor->bank_name && $license->vendor->bank_account_number)
                        <button type="button" class="btn btn-primary w-full text-base py-3" id="btnTransfer" onclick="prosesTransfer()">
                            🏦 Transfer ke Vendor — Xendit
                        </button>
                        @else
                        <button type="button" class="btn btn-secondary w-full text-base py-3 opacity-50 cursor-not-allowed" disabled>
                            🏦 Rekening vendor belum tersedia
                        </button>
                        @endif
                    </div>
                </form>
            </div>

            {{-- ORDER SUMMARY SIDEBAR (2/5) --}}
            <div class="lg:col-span-2 space-y-4">
                <div class="card">
                    <h3 class="text-sm font-semibold uppercase tracking-wider mb-4" style="color: var(--color-text-secondary);">Detail Pembayaran</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span style="color: var(--color-text-secondary);">License</span>
                            <span class="font-medium text-right" style="max-width: 60%;">{{ $license->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span style="color: var(--color-text-secondary);">Vendor</span>
                            <span class="font-medium">{{ optional($license->vendor)->name ?? '—' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span style="color: var(--color-text-secondary);">Bank</span>
                            <span class="font-medium">{{ optional($license->vendor)->bank_name ?? '—' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span style="color: var(--color-text-secondary);">No. Rekening</span>
                            <span class="font-mono font-medium">{{ optional($license->vendor)->bank_account_number ?? '—' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span style="color: var(--color-text-secondary);">Billing Cycle</span>
                            <span class="font-medium capitalize">{{ str_replace('_', ' ', $license->billing_cycle) }}</span>
                        </div>
                        @if($license->seats)
                        <div class="flex justify-between">
                            <span style="color: var(--color-text-secondary);">Seats</span>
                            <span class="font-medium">{{ $license->seats }} users</span>
                        </div>
                        @endif
                        <hr style="border-color: var(--color-border);">
                        <div class="flex justify-between text-lg pt-2" style="border-top: 1px solid var(--color-border);">
                            <span class="font-bold">Total Transfer</span>
                            <span class="font-bold" style="color: var(--color-primary);">{{ $license->formatted_cost }}</span>
                        </div>
                    </div>
                </div>

                {{-- Xendit Info --}}
                <div class="card text-center">
                    <svg class="w-8 h-8 mx-auto mb-2" style="color: var(--color-status-active);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    <p class="text-xs font-medium" style="color: var(--color-text-primary);">Transfer Aman</p>
                    <p class="text-[10px] mt-1" style="color: var(--color-text-secondary);">Diproses melalui Xendit Disbursement API yang tersertifikasi PCI-DSS</p>
                </div>

                {{-- Disbursement Flow Info --}}
                <div class="card">
                    <h3 class="text-sm font-semibold uppercase tracking-wider mb-3" style="color: var(--color-text-secondary);">Alur Disbursement</h3>
                    <div class="space-y-3">
                        <div class="flex items-start gap-2">
                            <div class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold shrink-0 mt-0.5" style="background: var(--color-primary); color: white;">1</div>
                            <p class="text-xs" style="color: var(--color-text-secondary);">Klik <strong>"Transfer ke Vendor"</strong> untuk mengirim request disbursement ke Xendit</p>
                        </div>
                        <div class="flex items-start gap-2">
                            <div class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold shrink-0 mt-0.5" style="background: var(--color-primary); color: white;">2</div>
                            <p class="text-xs" style="color: var(--color-text-secondary);">Xendit memproses transfer ke rekening vendor</p>
                        </div>
                        <div class="flex items-start gap-2">
                            <div class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold shrink-0 mt-0.5" style="background: var(--color-primary); color: white;">3</div>
                            <p class="text-xs" style="color: var(--color-text-secondary);">Status otomatis di-update via callback: <strong>PENDING → COMPLETED</strong></p>
                        </div>
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

    @push('scripts')
    <script>
        function prosesTransfer() {
            if (confirm('Apakah Anda yakin ingin mentransfer dana ke vendor ini via Xendit?')) {
                // Ubah state tombol agar tidak diklik dua kali
                let btn = document.getElementById('btnTransfer');
                btn.disabled = true;
                btn.innerText = '\u23f3 Sedang memproses...';

                // Submit form secara manual
                document.getElementById('disbursementForm').submit();
            }
        }
    </script>
    @endpush

</x-app-layout>
