<x-app-layout title="Process Payment" :breadcrumbs="[['label' => 'Finance', 'url' => '#'], ['label' => 'Process Payment']]">

    <div class="max-w-3xl mx-auto">

        {{-- HEADER --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xl font-bold" style="color: var(--color-text-primary);">Process Payment</h2>
                <p class="text-sm mt-1" style="color: var(--color-text-secondary);">Perpanjangan lisensi perangkat lunak</p>
            </div>
            <a href="{{ route('licenses.show', $license) }}" class="btn btn-secondary text-sm">← Back to License</a>
        </div>

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

        {{-- PAYMENT DETAILS --}}
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

            {{-- PAYMENT FORM (3/5) --}}
            <div class="lg:col-span-3">
                <div class="card">
                    <h3 class="text-sm font-semibold uppercase tracking-wider mb-4" style="color: var(--color-text-secondary);">Payment Information</h3>

                    <form method="POST" action="{{ route('payments.store') }}" id="payment-form"
                          x-data="paymentForm()" @submit.prevent="processPayment()">
                        @csrf
                        <input type="hidden" name="license_id" value="{{ $license->id }}">

                        <div class="space-y-4">
                            {{-- Amount --}}
                            <div>
                                <label class="form-label">Amount (Rp) <span style="color: var(--color-status-danger);">*</span></label>
                                <input type="number" name="amount" class="form-input text-lg font-bold"
                                       value="{{ (int) $license->cost }}" required min="1"
                                       x-model="amount" @input="updateTotal()">
                                <p class="text-xs mt-1" style="color: var(--color-text-secondary);">Biaya lisensi berdasarkan data terakhir</p>
                            </div>

                            {{-- Payment Date --}}
                            <div>
                                <label class="form-label">Payment Date <span style="color: var(--color-status-danger);">*</span></label>
                                <input type="date" name="payment_date" class="form-input" value="{{ now()->format('Y-m-d') }}" required>
                            </div>

                            {{-- Payment Method --}}
                            <div>
                                <label class="form-label">Payment Method <span style="color: var(--color-status-danger);">*</span></label>
                                <div class="grid grid-cols-2 gap-3 mt-2">
                                    <label class="flex items-center gap-3 p-3 rounded-xl cursor-pointer transition"
                                           :class="method === 'midtrans' ? 'ring-2 ring-indigo-500' : ''"
                                           style="background: var(--color-content-bg); border: 1px solid var(--color-border);">
                                        <input type="radio" name="payment_method" value="midtrans" x-model="method" class="w-4 h-4">
                                        <div>
                                            <p class="text-sm font-medium">Midtrans</p>
                                            <p class="text-[10px]" style="color: var(--color-text-secondary);">Payment Gateway</p>
                                        </div>
                                    </label>
                                    <label class="flex items-center gap-3 p-3 rounded-xl cursor-pointer transition"
                                           :class="method === 'transfer' ? 'ring-2 ring-indigo-500' : ''"
                                           style="background: var(--color-content-bg); border: 1px solid var(--color-border);">
                                        <input type="radio" name="payment_method" value="transfer" x-model="method" class="w-4 h-4">
                                        <div>
                                            <p class="text-sm font-medium">Bank Transfer</p>
                                            <p class="text-[10px]" style="color: var(--color-text-secondary);">BCA, Mandiri, BNI</p>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            {{-- Bank Info (shown for bank transfer) --}}
                            @if($license->vendor->bank_name)
                            <div x-show="method === 'transfer'" x-transition class="p-3 rounded-lg" style="background: var(--color-status-info-bg); border: 1px solid var(--color-border);">
                                <p class="text-xs font-semibold uppercase mb-1" style="color: var(--color-status-info);">Vendor Bank Account</p>
                                <p class="text-sm font-medium">{{ $license->vendor->bank_name }}</p>
                                <p class="text-sm font-mono font-bold" style="color: var(--color-text-primary);">{{ $license->vendor->bank_account_number }}</p>
                                <p class="text-xs mt-1" style="color: var(--color-text-secondary);">a.n. {{ $license->vendor->name }}</p>
                            </div>
                            @endif

                            {{-- Reference Number --}}
                            <div x-show="method === 'transfer'" x-transition>
                                <label class="form-label">Reference Number</label>
                                <input type="text" name="reference_number" class="form-input font-mono" placeholder="e.g. TRF-20260507-001">
                            </div>

                            {{-- Notes --}}
                            <div>
                                <label class="form-label">Notes</label>
                                <textarea name="notes" class="form-input" rows="2" placeholder="Catatan pembayaran...">Perpanjangan {{ $license->name }}</textarea>
                            </div>
                        </div>

                        <div class="mt-6 pt-4" style="border-top: 1px solid var(--color-border);">
                            {{-- Midtrans Pay Button --}}
                            <button type="button" x-show="method === 'midtrans'" x-transition
                                    @click="payWithMidtrans()"
                                    class="btn btn-primary w-full text-base py-3" :disabled="processing">
                                <span x-text="processing ? '⏳ Processing...' : '💳 Pay with Midtrans — Rp ' + formatRupiah(amount)"></span>
                            </button>
                            {{-- Manual Submit Button --}}
                            <button type="submit" x-show="method === 'transfer'" x-transition
                                    class="btn btn-primary w-full text-base py-3" :disabled="processing">
                                <span x-text="processing ? '⏳ Submitting...' : '📤 Submit Payment Request — Rp ' + formatRupiah(amount)"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ORDER SUMMARY SIDEBAR (2/5) --}}
            <div class="lg:col-span-2 space-y-4">
                <div class="card">
                    <h3 class="text-sm font-semibold uppercase tracking-wider mb-4" style="color: var(--color-text-secondary);">Order Summary</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span style="color: var(--color-text-secondary);">License</span>
                            <span class="font-medium text-right" style="max-width: 60%;">{{ $license->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span style="color: var(--color-text-secondary);">Vendor</span>
                            <span class="font-medium">{{ $license->vendor->name ?? '—' }}</span>
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
                        <div class="flex justify-between">
                            <span style="color: var(--color-text-secondary);">Subtotal</span>
                            <span class="font-semibold">{{ $license->formatted_cost }}</span>
                        </div>
                        <div class="flex justify-between text-lg pt-2" style="border-top: 1px solid var(--color-border);">
                            <span class="font-bold">Total</span>
                            <span class="font-bold" style="color: var(--color-primary);" x-data x-text="'Rp ' + paymentForm().formatRupiah({{ (int) $license->cost }})">{{ $license->formatted_cost }}</span>
                        </div>
                    </div>
                </div>

                {{-- Security Notice --}}
                <div class="card text-center">
                    <svg class="w-8 h-8 mx-auto mb-2" style="color: var(--color-status-active);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    <p class="text-xs font-medium" style="color: var(--color-text-primary);">Pembayaran Aman</p>
                    <p class="text-[10px] mt-1" style="color: var(--color-text-secondary);">Diproses melalui Midtrans Payment Gateway yang tersertifikasi PCI-DSS</p>
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

    {{-- Midtrans Snap JS --}}
    @push('scripts')
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
    <script>
        function paymentForm() {
            return {
                method: 'midtrans',
                amount: {{ (int) $license->cost }},
                processing: false,

                formatRupiah(num) {
                    return new Intl.NumberFormat('id-ID').format(num);
                },

                updateTotal() {
                    // Could add tax logic here
                },

                async payWithMidtrans() {
                    this.processing = true;
                    try {
                        const response = await fetch('{{ route("midtrans.token") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            },
                            body: JSON.stringify({
                                license_id: {{ $license->id }},
                                amount: this.amount,
                            }),
                        });

                        const data = await response.json();

                        if (data.snap_token) {
                            window.snap.pay(data.snap_token, {
                                onSuccess: (result) => {
                                    window.location.href = '{{ route("payments.index") }}?success=1';
                                },
                                onPending: (result) => {
                                    window.location.href = '{{ route("payments.index") }}?pending=1';
                                },
                                onError: (result) => {
                                    alert('Payment gagal. Silakan coba lagi.');
                                    this.processing = false;
                                },
                                onClose: () => {
                                    this.processing = false;
                                },
                            });
                        } else {
                            alert(data.error || 'Gagal mendapatkan token pembayaran.');
                            this.processing = false;
                        }
                    } catch (err) {
                        alert('Terjadi kesalahan. Silakan coba lagi.');
                        this.processing = false;
                    }
                },

                processPayment() {
                    if (this.method === 'transfer') {
                        this.processing = true;
                        this.$el.submit();
                    }
                }
            };
        }
    </script>
    @endpush

</x-app-layout>
