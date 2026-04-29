<x-app-layout title="Invoices" :breadcrumbs="[['label' => 'Finance', 'url' => '#'], ['label' => 'Invoices']]">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold" style="color: var(--color-text-primary);">Invoices</h2>
            <p class="text-sm mt-1" style="color: var(--color-text-secondary);">Kelola invoice internal dan dari vendor</p>
        </div>
        <button class="btn btn-primary" @click="$dispatch('open-modal-create-invoice')" id="create-invoice-btn">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Create Invoice
        </button>
    </div>

    {{-- SUMMARY CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <x-stat-card label="Total Invoices" :value="48" variant="info" />
        <x-stat-card label="Unpaid Amount" :value="96000000" variant="warning" prefix="Rp " />
        <x-stat-card label="Paid This Month" :value="75500000" variant="active" prefix="Rp " />
    </div>

    {{-- INVOICE TABLE --}}
    <div class="card p-0 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table" id="invoices-table">
                <thead><tr><th>Invoice #</th><th>License</th><th>Vendor</th><th>Issued</th><th>Due</th><th>Amount</th><th>Status</th><th class="text-center">Actions</th></tr></thead>
                <tbody>
                    @php
                    $invoices = [
                        ['inv' => 'INV-2026-052', 'license' => 'Kaspersky Endpoint', 'vendor' => 'Kaspersky', 'issued' => '10 Apr 2026', 'due' => '15 Apr 2026', 'amount' => 8500000, 'status' => 'pending'],
                        ['inv' => 'INV-2026-051', 'license' => 'Oracle Database', 'vendor' => 'Oracle', 'issued' => '07 Apr 2026', 'due' => '21 Apr 2026', 'amount' => 45000000, 'status' => 'pending'],
                        ['inv' => 'INV-2026-050', 'license' => 'Microsoft 365', 'vendor' => 'Microsoft', 'issued' => '05 Apr 2026', 'due' => '28 Apr 2026', 'amount' => 12000000, 'status' => 'pending'],
                        ['inv' => 'INV-2026-048', 'license' => 'Adobe CC', 'vendor' => 'Adobe', 'issued' => '01 Apr 2026', 'due' => '10 Apr 2026', 'amount' => 18000000, 'status' => 'paid'],
                        ['inv' => 'INV-2026-047', 'license' => 'VMware vSphere', 'vendor' => 'VMware', 'issued' => '25 Mar 2026', 'due' => '08 Apr 2026', 'amount' => 32000000, 'status' => 'paid'],
                    ];
                    @endphp
                    @foreach($invoices as $inv)
                    <tr>
                        <td class="font-mono text-xs font-medium" style="color: var(--color-primary);">{{ $inv['inv'] }}</td>
                        <td class="font-medium">{{ $inv['license'] }}</td>
                        <td>{{ $inv['vendor'] }}</td>
                        <td>{{ $inv['issued'] }}</td>
                        <td>{{ $inv['due'] }}</td>
                        <td class="font-semibold">Rp {{ number_format($inv['amount'], 0, ',', '.') }}</td>
                        <td><x-status-badge :status="$inv['status']" size="sm" /></td>
                        <td class="text-center">
                            <div class="flex items-center justify-center gap-1">
                                <button class="btn-ghost p-1.5 rounded-lg" title="View"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></button>
                                <button class="btn-ghost p-1.5 rounded-lg" title="Download PDF"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg></button>
                                <button class="btn-ghost p-1.5 rounded-lg" title="Print"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg></button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- CREATE INVOICE MODAL --}}
    <x-modal id="create-invoice" title="Create Invoice" maxWidth="md">
        <form>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div class="md:col-span-2"><label class="form-label">Related License *</label><select class="form-input"><option>Pilih Lisensi...</option><option>Kaspersky Endpoint</option><option>Oracle Database</option><option>Microsoft 365</option></select></div>
                <div><label class="form-label">Issue Date *</label><input type="date" class="form-input"></div>
                <div><label class="form-label">Due Date *</label><input type="date" class="form-input"></div>
                <div class="md:col-span-2"><label class="form-label">Amount (Rp) *</label><input type="number" class="form-input" placeholder="0"></div>
                <div class="md:col-span-2"><label class="form-label">Notes</label><textarea class="form-input" rows="2" placeholder="Catatan invoice..."></textarea></div>
            </div>
            <x-slot:footer>
                <button type="button" @click="hide()" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary">Create Invoice</button>
            </x-slot:footer>
        </form>
    </x-modal>

</x-app-layout>
