<x-app-layout title="Payments" :breadcrumbs="[['label' => 'Finance', 'url' => '#'], ['label' => 'Process Payment']]">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold" style="color: var(--color-text-primary);">Process Payment</h2>
            <p class="text-sm mt-1" style="color: var(--color-text-secondary);">Kelola pembayaran lisensi dan perpanjangan</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('payments.history') }}" class="btn btn-secondary text-sm">📋 Payment History</a>
            <button class="btn btn-primary" @click="$dispatch('open-modal-new-payment')" id="new-payment-btn">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                New Payment
            </button>
        </div>
    </div>

    {{-- SUMMARY CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-6">
        <x-stat-card label="Pending" :value="$pendingCount" variant="warning" />
        <x-stat-card label="Pending Amount" :value="$pendingAmount" variant="warning" prefix="Rp " />
        <x-stat-card label="Approved" :value="$approvedCount" variant="info" />
        <x-stat-card label="Total Paid" :value="$paidTotal" variant="active" prefix="Rp " />
    </div>

    {{-- FILTER --}}
    <div class="card mb-6">
        <form method="GET" action="{{ route('payments.index') }}" class="flex flex-col md:flex-row gap-3">
            <select name="status" class="form-input w-full md:w-48" onchange="this.form.submit()">
                <option value="">All Status</option>
                <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                <option value="approved" @selected(request('status') === 'approved')>Approved</option>
                <option value="paid" @selected(request('status') === 'paid')>Paid</option>
                <option value="rejected" @selected(request('status') === 'rejected')>Rejected</option>
            </select>
        </form>
    </div>

    {{-- PAYMENTS TABLE --}}
    <div class="card p-0 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table" id="payments-table">
                <thead><tr><th>License</th><th>Vendor</th><th>Date</th><th>Amount</th><th>Method</th><th>Status</th><th>Created By</th><th class="text-center">Actions</th></tr></thead>
                <tbody>
                    @forelse($payments as $payment)
                    <tr>
                        <td class="font-medium">
                            <a href="{{ route('licenses.show', $payment->license) }}" class="hover:underline" style="color: var(--color-primary);">{{ $payment->license->name }}</a>
                        </td>
                        <td>{{ $payment->license->vendor->name ?? '—' }}</td>
                        <td>{{ $payment->payment_date->format('d M Y') }}</td>
                        <td class="font-semibold">Rp {{ number_format((float)$payment->amount, 0, ',', '.') }}</td>
                        <td><span class="text-xs capitalize">{{ str_replace('_', ' ', $payment->payment_method ?? '—') }}</span></td>
                        <td><x-status-badge :status="$payment->status" size="sm" /></td>
                        <td class="text-xs">{{ $payment->creator->name ?? '—' }}</td>
                        <td class="text-center">
                            <div class="flex items-center justify-center gap-1">
                                @if($payment->status === 'pending')
                                    <form method="POST" action="{{ route('payments.approve', $payment) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="btn btn-primary text-xs py-1 px-2" title="Approve">✅ Approve</button>
                                    </form>
                                    <form method="POST" action="{{ route('payments.reject', $payment) }}" class="inline" onsubmit="return confirm('Tolak payment ini?')">
                                        @csrf
                                        <button type="submit" class="btn btn-danger text-xs py-1 px-2" title="Reject">❌</button>
                                    </form>
                                @elseif($payment->status === 'approved')
                                    <div x-data="{ open: false }">
                                        <button @click="open = !open" class="btn btn-primary text-xs py-1 px-2">💳 Mark Paid</button>
                                        <form method="POST" action="{{ route('payments.markPaid', $payment) }}" x-show="open" x-transition class="flex items-center gap-1 mt-1">
                                            @csrf
                                            <select name="payment_method" class="form-input text-xs py-1 w-24" required>
                                                <option value="transfer">Transfer</option>
                                                <option value="credit_card">CC</option>
                                                <option value="e_wallet">E-Wallet</option>
                                                <option value="cash">Cash</option>
                                            </select>
                                            <input type="text" name="reference_number" placeholder="Ref#" class="form-input text-xs py-1 w-20">
                                            <button type="submit" class="btn btn-primary text-xs py-1 px-2">OK</button>
                                        </form>
                                    </div>
                                @elseif($payment->status === 'paid')
                                    <span class="text-xs" style="color: var(--color-text-secondary);">
                                        {{ $payment->approver->name ?? '' }}
                                    </span>
                                @else
                                    <span class="text-xs" style="color: var(--color-text-secondary);">—</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-8">
                            <p class="text-sm" style="color: var(--color-text-secondary);">Belum ada payment.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($payments->hasPages())
        <div class="px-6 py-3 flex items-center justify-between" style="border-top: 1px solid var(--color-border);">
            <span class="text-xs" style="color: var(--color-text-secondary);">Showing {{ $payments->firstItem() }}–{{ $payments->lastItem() }} of {{ $payments->total() }}</span>
            <div>{{ $payments->links('pagination::tailwind') }}</div>
        </div>
        @endif
    </div>

    {{-- NEW PAYMENT MODAL --}}
    <x-modal id="new-payment" title="New Payment Request" maxWidth="md">
        <form method="POST" action="{{ route('payments.store') }}" id="new-payment-form">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="form-label">License <span style="color: var(--color-status-danger);">*</span></label>
                    <select name="license_id" class="form-input" required>
                        <option value="" disabled selected>Pilih Lisensi...</option>
                        @foreach($licenses as $license)
                            <option value="{{ $license->id }}">{{ $license->name }} — {{ $license->vendor->name ?? '' }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Amount (Rp) <span style="color: var(--color-status-danger);">*</span></label>
                    <input type="number" name="amount" class="form-input" placeholder="0" required min="1">
                </div>
                <div>
                    <label class="form-label">Payment Date <span style="color: var(--color-status-danger);">*</span></label>
                    <input type="date" name="payment_date" class="form-input" value="{{ now()->format('Y-m-d') }}" required>
                </div>
                <div>
                    <label class="form-label">Payment Method</label>
                    <select name="payment_method" class="form-input">
                        <option value="">— Belum ditentukan —</option>
                        <option value="transfer">Bank Transfer</option>
                        <option value="credit_card">Credit Card</option>
                        <option value="e_wallet">E-Wallet</option>
                        <option value="cash">Cash</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-input" rows="2" placeholder="Catatan pembayaran..."></textarea>
                </div>
            </div>
            <x-slot:footer>
                <button type="button" @click="hide()" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary">💳 Submit Payment</button>
            </x-slot:footer>
        </form>
    </x-modal>

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
