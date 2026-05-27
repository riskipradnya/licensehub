<x-app-layout title="Payment History" :breadcrumbs="[['label' => 'Finance', 'url' => '#'], ['label' => 'Payment History']]">

    <div x-data="{ showExportModal: false }">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h2 class="text-xl font-bold" style="color: var(--color-text-primary);">Payment History</h2>
                <p class="text-sm mt-1" style="color: var(--color-text-secondary);">Riwayat seluruh pembayaran lisensi</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <button @click="showExportModal = true" class="btn px-4 py-2 bg-indigo-50 text-indigo-600 hover:bg-indigo-100 dark:bg-indigo-900/30 dark:text-indigo-300 dark:hover:bg-indigo-900/50 rounded-lg text-sm font-semibold transition-colors border border-indigo-200 dark:border-indigo-800 flex items-center gap-2">
                    📥 Export Data
                </button>
                <a href="{{ route('payments.index') }}" class="btn btn-secondary text-sm">← Back to Payments</a>
            </div>
        </div>

        {{-- Export Modal --}}
        <div x-show="showExportModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-0" style="display: none;" aria-labelledby="modal-title" role="dialog" aria-modal="true" x-cloak>
            <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" @click="showExportModal = false" x-transition.opacity></div>
            
            <div class="relative z-10 w-full max-w-lg bg-white dark:bg-[#1E293B] rounded-2xl shadow-xl overflow-hidden" @click.stop x-transition.scale.origin.bottom>
                <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800">
                    <h3 class="text-lg leading-6 font-bold text-gray-900 dark:text-white" id="modal-title">Export Payment History</h3>
                </div>
                
                <div class="px-6 py-5">
                    <form id="exportForm" method="GET" action="{{ route('payments.export') }}">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Date</label>
                                <input type="date" name="start_date" class="form-input w-full rounded-lg bg-white dark:bg-[#1E293B] text-slate-900 dark:text-white border-slate-300 dark:border-slate-700 focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Date</label>
                                <input type="date" name="end_date" class="form-input w-full rounded-lg bg-white dark:bg-[#1E293B] text-slate-900 dark:text-white border-slate-300 dark:border-slate-700 focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                        </div>
                        
                        <input type="hidden" name="format" id="exportFormat" value="pdf">
                        
                        <div class="flex flex-col sm:flex-row gap-3 justify-end pt-2">
                            <button type="button" @click="showExportModal = false" class="btn px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 bg-white dark:bg-transparent rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                                Cancel
                            </button>
                            <button type="button" onclick="document.getElementById('exportFormat').value='pdf'; document.getElementById('exportForm').submit();" class="btn px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium shadow-sm flex items-center justify-center gap-2 transition-colors">
                                📄 Export PDF
                            </button>
                            <button type="button" onclick="document.getElementById('exportFormat').value='excel'; document.getElementById('exportForm').submit();" class="btn px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-medium shadow-sm flex items-center justify-center gap-2 transition-colors">
                                📊 Export Excel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    {{-- FILTERS --}}
    <div class="card mb-6">
        <form method="GET" action="{{ route('payments.history') }}" class="flex flex-col md:flex-row gap-3">
            <select name="status" class="form-input w-full md:w-40" onchange="this.form.submit()">
                <option value="">All Status</option>
                <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                <option value="approved" @selected(request('status') === 'approved')>Approved</option>
                <option value="paid" @selected(request('status') === 'paid')>Paid</option>
                <option value="rejected" @selected(request('status') === 'rejected')>Rejected</option>
            </select>
            <select name="method" class="form-input w-full md:w-40" onchange="this.form.submit()">
                <option value="">All Methods</option>
                <option value="transfer" @selected(request('method') === 'transfer')>Bank Transfer</option>
                <option value="credit_card" @selected(request('method') === 'credit_card')>Credit Card</option>
                <option value="e_wallet" @selected(request('method') === 'e_wallet')>E-Wallet</option>
                <option value="cash" @selected(request('method') === 'cash')>Cash</option>
            </select>
        </form>
    </div>

    {{-- TABLE --}}
    <div class="card p-0 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table" id="payment-history-table">
                <thead><tr><th>Ref #</th><th>License</th><th>Amount</th><th>Method</th><th>Date</th><th>Created By</th><th class="text-center">Status</th><th class="text-center">Actions</th></tr></thead>
                <tbody>
                    @forelse($payments as $pay)
                    <tr>
                        <td class="font-mono text-xs font-medium" style="color: var(--color-primary);">{{ $pay->reference_number ?? '—' }}</td>
                        <td class="font-medium">{{ $pay->license?->name ?? '—' }}</td>
                        <td class="font-semibold">Rp {{ number_format((float)$pay->amount, 0, ',', '.') }}</td>
                        <td><span class="text-xs capitalize">{{ str_replace('_', ' ', $pay->payment_method ?? '—') }}</span></td>
                        <td>{{ $pay->payment_date->format('d M Y') }}</td>
                        <td class="text-xs">{{ $pay->creator->name ?? '—' }}</td>
                        <td class="text-center"><x-status-badge :status="$pay->status" size="sm" /></td>
                        <td class="text-center">
                            @if($pay->status === 'paid')
                                <a href="{{ route('payments.receipt', $pay->id) }}" class="btn-ghost p-1.5 rounded-lg text-indigo-600 hover:bg-indigo-50" title="Download Receipt">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </a>
                            @else
                                <span class="text-xs text-gray-400">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-8">
                            <p class="text-sm" style="color: var(--color-text-secondary);">Belum ada riwayat pembayaran.</p>
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
    </div>

</x-app-layout>
