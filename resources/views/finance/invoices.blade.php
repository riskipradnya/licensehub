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
        <x-stat-card label="Total Invoices" :value="$totalInvoices" variant="info" />
        <x-stat-card label="Unpaid Amount" :value="$unpaidAmount" variant="warning" prefix="Rp " />
        <x-stat-card label="Paid This Month" :value="$paidThisMonth" variant="active" prefix="Rp " />
    </div>

    {{-- FILTER --}}
    <div class="card mb-6">
        <form method="GET" action="{{ route('invoices.index') }}" class="grid grid-cols-1 md:grid-cols-12 gap-4 w-full">
            <div class="relative md:col-span-6">
                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2" style="color: var(--color-text-secondary);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari invoice/vendor..." class="form-input pl-10 w-full">
            </div>
            <div class="md:col-span-2">
                <select name="vendor_id" class="form-input w-full" onchange="this.form.submit()">
                    <option value="">All Vendors</option>
                    @foreach($vendors as $vendor)
                        <option value="{{ $vendor->id }}" @selected(request('vendor_id') == $vendor->id)>{{ $vendor->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2">
                <select name="status" class="form-input w-full" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="unpaid" @selected(request('status') === 'unpaid')>Unpaid</option>
                    <option value="paid" @selected(request('status') === 'paid')>Paid</option>
                    <option value="overdue" @selected(request('status') === 'overdue')>Overdue</option>
                </select>
            </div>
            <div class="md:col-span-2">
                <select name="year" class="form-input w-full" onchange="this.form.submit()">
                    <option value="">All Years</option>
                    @foreach($years as $yr)
                        <option value="{{ $yr }}" @selected(request('year') == $yr)>{{ $yr }}</option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    {{-- INVOICE TABLE --}}
    <div class="card p-0 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table" id="invoices-table">
                <thead><tr><th>Invoice #</th><th>License</th><th>Vendor</th><th>Issued</th><th>Due</th><th>Amount</th><th>Status</th><th class="text-center">Actions</th></tr></thead>
                <tbody>
                    @forelse($invoices as $inv)
                    <tr>
                        <td class="font-mono text-xs font-medium" style="color: var(--color-primary);">{{ $inv->invoice_number }}</td>
                        <td class="font-medium">{{ $inv->license->name ?? '—' }}</td>
                        <td>{{ $inv->vendor->name ?? '—' }}</td>
                        <td>{{ $inv->invoice_date->format('d M Y') }}</td>
                        <td>
                            @if($inv->isOverdue())
                                <span style="color: var(--color-status-danger);">{{ $inv->due_date->format('d M Y') }}</span>
                            @else
                                {{ $inv->due_date?->format('d M Y') ?? '—' }}
                            @endif
                        </td>
                        <td class="font-semibold">Rp {{ number_format((float)$inv->total_amount, 0, ',', '.') }}</td>
                        <td><x-status-badge :status="$inv->status === 'sent' ? 'expiring' : ($inv->status === 'overdue' ? 'expired' : $inv->status)" :label="ucfirst($inv->status)" size="sm" /></td>
                        <td class="text-center">
                            <div class="flex items-center justify-center gap-1" x-data="{ showStatus: false }">
                                {{-- Download File --}}
                                @if($inv->file_path)
                                <a href="{{ \Illuminate\Support\Facades\Storage::url($inv->file_path) }}" target="_blank" class="btn-ghost p-1.5 rounded-lg text-indigo-600 hover:bg-indigo-50" title="Download Invoice File">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                </a>
                                @endif
                                
                                {{-- Status Change --}}
                                <div class="relative">
                                    <button @click="showStatus = !showStatus" class="btn-ghost p-1.5 rounded-lg" title="Change Status">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                    </button>
                                    <div x-show="showStatus" @click.away="showStatus = false" x-transition class="absolute right-0 mt-1 w-32 rounded-lg shadow-lg z-10" style="background: var(--color-card-bg); border: 1px solid var(--color-border);">
                                        @foreach(['unpaid', 'paid', 'overdue'] as $st)
                                            @if($st !== $inv->status)
                                            <form method="POST" action="{{ route('invoices.updateStatus', $inv) }}">
                                                @csrf
                                                <input type="hidden" name="status" value="{{ $st }}">
                                                <button type="submit" class="w-full text-left text-xs px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-800 capitalize">{{ $st }}</button>
                                            </form>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                                {{-- Delete --}}
                                <form method="POST" action="{{ route('invoices.destroy', $inv) }}" class="inline" onsubmit="return confirm('Hapus invoice ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-ghost p-1.5 rounded-lg hover:text-red-500" title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-8">
                            <p class="text-sm" style="color: var(--color-text-secondary);">Belum ada invoice.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($invoices->hasPages())
        <div class="px-6 py-3 flex items-center justify-between" style="border-top: 1px solid var(--color-border);">
            <span class="text-xs" style="color: var(--color-text-secondary);">Showing {{ $invoices->firstItem() }}–{{ $invoices->lastItem() }} of {{ $invoices->total() }}</span>
            <div>{{ $invoices->links('pagination::tailwind') }}</div>
        </div>
        @endif
    </div>

    {{-- CREATE INVOICE MODAL --}}
    <x-modal id="create-invoice" title="Create Invoice" maxWidth="md">
        <form method="POST" action="{{ route('invoices.store') }}" id="create-invoice-form" enctype="multipart/form-data" x-data="{ amount: '', dueDate: '{{ now()->addDays(14)->format('Y-m-d') }}' }">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div class="md:col-span-2">
                    <label class="form-label">Related License <span style="color: var(--color-status-danger);">*</span></label>
                    <select name="license_id" class="form-input" required @change="amount = parseFloat($event.target.options[$event.target.selectedIndex].dataset.price) || 0">
                        <option value="" disabled selected data-price="0">Pilih Lisensi...</option>
                        @foreach($licenses as $license)
                            <option value="{{ $license->id }}" data-price="{{ $license->cost }}">{{ $license->name }} — {{ $license->vendor->name ?? '' }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Issue Date <span style="color: var(--color-status-danger);">*</span></label>
                    <input type="date" name="invoice_date" class="form-input" value="{{ now()->format('Y-m-d') }}" required>
                </div>
                <div>
                    <label class="form-label">Due Date <span style="color: var(--color-status-danger);">*</span></label>
                    <input type="date" name="due_date" class="form-input" x-model="dueDate" required>
                </div>
                <div class="md:col-span-2">
                    <label class="form-label">Amount (Rp) <span style="color: var(--color-status-danger);">*</span></label>
                    <input type="number" name="amount" class="form-input [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none" placeholder="0" required min="1" x-model="amount">
                </div>
                <div class="md:col-span-2">
                    <label class="form-label">Vendor Invoice File (PDF/Image, max 10MB)</label>
                    <input type="file" name="vendor_invoice_file" class="form-input text-sm" accept=".pdf,.jpg,.jpeg,.png">
                </div>
                <div class="md:col-span-2">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-input" rows="2" placeholder="Catatan invoice..."></textarea>
                </div>
            </div>
            <x-slot:footer>
                <button type="button" @click="hide()" class="btn btn-secondary">Cancel</button>
                <button type="submit" form="create-invoice-form" class="btn btn-primary">📝 Create Invoice</button>
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
