<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Invoice;
use App\Models\License;
use App\Models\Vendor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    /**
     * Display invoice list with stats.
     */
    public function index(Request $request): View
    {
        $query = Invoice::with(['license', 'vendor', 'payment', 'creator'])->latest();

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('license', fn($q) => $q->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('vendor', fn($q) => $q->where('name', 'like', "%{$search}%"));
            });
        }

        $invoices = $query->paginate(15)->withQueryString();

        // Summary stats
        $totalInvoices = Invoice::count();
        $unpaidAmount  = (float) Invoice::unpaid()->sum('total_amount');
        $paidThisMonth = (float) Invoice::where('status', 'paid')
            ->whereMonth('updated_at', now()->month)
            ->whereYear('updated_at', now()->year)
            ->sum('total_amount');

        // Dropdowns for create modal
        $licenses = License::with('vendor')->orderBy('name')->get();
        $vendors  = Vendor::active()->ordered()->get();

        return view('finance.invoices', compact(
            'invoices', 'totalInvoices', 'unpaidAmount', 'paidThisMonth',
            'licenses', 'vendors',
        ));
    }

    /**
     * Store a new invoice.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'license_id'   => ['required', 'exists:licenses,id'],
            'amount'       => ['required', 'numeric', 'min:1'],
            'tax_amount'   => ['nullable', 'numeric', 'min:0'],
            'invoice_date' => ['required', 'date'],
            'due_date'     => ['required', 'date', 'after_or_equal:invoice_date'],
            'notes'        => ['nullable', 'string', 'max:2000'],
        ], [
            'license_id.required'        => 'Lisensi wajib dipilih.',
            'amount.required'            => 'Jumlah invoice wajib diisi.',
            'invoice_date.required'      => 'Tanggal invoice wajib diisi.',
            'due_date.required'          => 'Tanggal jatuh tempo wajib diisi.',
            'due_date.after_or_equal'    => 'Tanggal jatuh tempo harus setelah tanggal invoice.',
        ]);

        $license = License::with('vendor')->findOrFail($validated['license_id']);
        $taxAmount = $validated['tax_amount'] ?? 0;

        $invoice = Invoice::create([
            'invoice_number' => Invoice::generateInvoiceNumber(),
            'license_id'     => $validated['license_id'],
            'vendor_id'      => $license->vendor_id,
            'amount'         => $validated['amount'],
            'tax_amount'     => $taxAmount,
            'total_amount'   => $validated['amount'] + $taxAmount,
            'invoice_date'   => $validated['invoice_date'],
            'due_date'       => $validated['due_date'],
            'status'         => 'draft',
            'notes'          => $validated['notes'] ?? null,
            'created_by'     => auth()->id(),
        ]);

        AuditLog::log('created', 'Invoice', $invoice->id, null, [
            'invoice_number' => $invoice->invoice_number,
            'amount'         => $invoice->total_amount,
        ]);

        return redirect()
            ->route('invoices.index')
            ->with('success', "Invoice {$invoice->invoice_number} berhasil dibuat.");
    }

    /**
     * Update invoice status.
     */
    public function updateStatus(Request $request, Invoice $invoice): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:draft,sent,paid,overdue,cancelled'],
        ]);

        $oldStatus = $invoice->status;
        $invoice->update(['status' => $validated['status']]);

        AuditLog::log('updated_status', 'Invoice', $invoice->id,
            ['status' => $oldStatus],
            ['status' => $validated['status']]
        );

        return back()->with('success', "Status invoice {$invoice->invoice_number} diperbarui.");
    }

    /**
     * Delete an invoice.
     */
    public function destroy(Invoice $invoice): RedirectResponse
    {
        $invNumber = $invoice->invoice_number;
        $invoice->delete();

        AuditLog::log('deleted', 'Invoice', $invoice->id, ['invoice_number' => $invNumber]);

        return redirect()
            ->route('invoices.index')
            ->with('success', "Invoice {$invNumber} berhasil dihapus.");
    }
}
