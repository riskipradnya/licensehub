<x-app-layout title="Payments" :breadcrumbs="[['label' => 'Finance', 'url' => '#'], ['label' => 'Process Payment']]">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold" style="color: var(--color-text-primary);">Process Payment</h2>
            <p class="text-sm mt-1" style="color: var(--color-text-secondary);">Daftar lisensi yang perlu dibayarkan untuk perpanjangan</p>
        </div>
    </div>

    {{-- PENDING PAYMENTS --}}
    <div class="card p-0 overflow-hidden mb-6">
        <div class="px-6 py-4 flex items-center justify-between" style="border-bottom: 1px solid var(--color-border);">
            <h3 class="text-base font-semibold" style="color: var(--color-text-primary);">Pending Payments</h3>
            <span class="badge badge--warning">5 items</span>
        </div>
        <div class="overflow-x-auto">
            <table class="data-table" id="pending-payments-table">
                <thead><tr><th><input type="checkbox" class="w-4 h-4 rounded"></th><th>License</th><th>Vendor</th><th>Due Date</th><th>Amount</th><th>Priority</th><th>Action</th></tr></thead>
                <tbody>
                    @php
                    $pending = [
                        ['name' => 'Kaspersky Endpoint', 'vendor' => 'Kaspersky', 'due' => '15 Apr 2026', 'amount' => 8500000, 'priority' => 'danger', 'label' => 'H-1'],
                        ['name' => 'Oracle Database', 'vendor' => 'Oracle', 'due' => '21 Apr 2026', 'amount' => 45000000, 'priority' => 'warning', 'label' => 'H-7'],
                        ['name' => 'Microsoft 365', 'vendor' => 'Microsoft', 'due' => '28 Apr 2026', 'amount' => 12000000, 'priority' => 'warning', 'label' => 'H-14'],
                        ['name' => 'Windows Server', 'vendor' => 'Microsoft', 'due' => '05 May 2026', 'amount' => 25000000, 'priority' => 'info', 'label' => 'H-21'],
                        ['name' => 'ESET NOD32', 'vendor' => 'VHP', 'due' => '10 May 2026', 'amount' => 5500000, 'priority' => 'info', 'label' => 'H-26'],
                    ];
                    @endphp
                    @foreach($pending as $p)
                    <tr>
                        <td><input type="checkbox" class="w-4 h-4 rounded"></td>
                        <td class="font-medium">{{ $p['name'] }}</td>
                        <td>{{ $p['vendor'] }}</td>
                        <td>{{ $p['due'] }}</td>
                        <td class="font-semibold">Rp {{ number_format($p['amount'], 0, ',', '.') }}</td>
                        <td><x-status-badge :status="$p['priority']" :label="$p['label']" size="sm" /></td>
                        <td>
                            <button class="btn btn-primary text-xs py-1.5 px-3" @click="$dispatch('open-modal-payment')">
                                💳 Pay Now
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr><td colspan="4" class="text-right font-bold">Total Outstanding</td>
                        <td class="font-bold" style="color: var(--color-primary);">Rp 96.000.000</td><td colspan="2"></td></tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- PAYMENT MODAL --}}
    <x-modal id="payment" title="Process Payment" maxWidth="md">
        <div x-data="{ method: 'bank_transfer' }">
            <div class="card mb-4" style="border-left: 4px solid var(--color-primary);">
                <p class="text-sm font-medium">Kaspersky Endpoint Security</p>
                <p class="text-xl font-bold mt-1" style="color: var(--color-primary);">Rp 8.500.000</p>
                <p class="text-xs mt-1" style="color: var(--color-text-secondary);">Due: 15 Apr 2026 • Vendor: Kaspersky Lab</p>
            </div>

            <div class="mb-4">
                <label class="form-label">Payment Method</label>
                <div class="grid grid-cols-2 gap-3 mt-2">
                    <label class="flex items-center gap-2 p-3 rounded-xl cursor-pointer transition" :class="method === 'bank_transfer' ? 'ring-2 ring-indigo-500' : ''" style="background: var(--color-content-bg); border: 1px solid var(--color-border);">
                        <input type="radio" name="method" value="bank_transfer" x-model="method" class="w-4 h-4">
                        <div><p class="text-sm font-medium">Bank Transfer</p><p class="text-[10px]" style="color: var(--color-text-secondary);">BCA, Mandiri, BNI</p></div>
                    </label>
                    <label class="flex items-center gap-2 p-3 rounded-xl cursor-pointer transition" :class="method === 'midtrans' ? 'ring-2 ring-indigo-500' : ''" style="background: var(--color-content-bg); border: 1px solid var(--color-border);">
                        <input type="radio" name="method" value="midtrans" x-model="method" class="w-4 h-4">
                        <div><p class="text-sm font-medium">Midtrans</p><p class="text-[10px]" style="color: var(--color-text-secondary);">Payment Gateway</p></div>
                    </label>
                </div>
            </div>

            <div class="mb-4" x-show="method === 'bank_transfer'">
                <label class="form-label">Upload Bukti Transfer</label>
                <x-file-upload name="proof" accept=".pdf,.jpg,.png" maxSize="5" />
            </div>

            <div class="mb-4">
                <label class="form-label">Notes</label>
                <textarea class="form-input" rows="2" placeholder="Catatan pembayaran..."></textarea>
            </div>

            <x-slot:footer>
                <button @click="hide()" class="btn btn-secondary">Cancel</button>
                <button class="btn btn-primary">💳 Confirm Payment</button>
            </x-slot:footer>
        </div>
    </x-modal>

</x-app-layout>
