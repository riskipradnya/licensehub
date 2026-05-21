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
        $query = Invoice::with(['license', 'vendor', 'payment', 'creator']);

        if ($status = $request->input('status')) {
            if ($status === 'unpaid') {
                $query->unpaid();
            } else {
                $query->where('status', $status);
            }
        }
        
        if ($vendorId = $request->input('vendor_id')) {
            $query->where('vendor_id', $vendorId);
        }

        if ($year = $request->input('year')) {
            $query->whereYear('invoice_date', $year);
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('license', fn($q) => $q->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('vendor', fn($q) => $q->where('name', 'like', "%{$search}%"));
            });
        }

        // Urutkan berdasarkan tanggal jatuh tempo terdekat
        $query->orderBy('due_date', 'asc');

        $invoices = $query->paginate(15)->withQueryString();

        // Summary stats
        $totalInvoices = Invoice::count();
        $unpaidAmount  = (float) Invoice::unpaid()->sum('total_amount');
        $paidThisMonth = (float) Invoice::where('status', 'paid')
            ->whereMonth('updated_at', now()->month)
            ->whereYear('updated_at', now()->year)
            ->sum('total_amount');

        // Dropdowns for create modal & filters
        $licenses = License::with('vendor')->orderBy('name')->get();
        $vendors  = Vendor::active()->ordered()->get();
        
        // Mengambil daftar tahun yang ada dari data invoice
        $years = Invoice::selectRaw('YEAR(invoice_date) as year')->distinct()->pluck('year')->filter()->sortDesc();

        return view('finance.invoices', compact(
            'invoices', 'totalInvoices', 'unpaidAmount', 'paidThisMonth',
            'licenses', 'vendors', 'years'
        ));
    }

    /**
     * Store a new invoice.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'license_id'          => ['required', 'exists:licenses,id'],
            'amount'              => ['required', 'numeric', 'min:1'],
            'invoice_date'        => ['required', 'date'],
            'due_date'            => ['required', 'date', 'after_or_equal:invoice_date'],
            'notes'               => ['nullable', 'string', 'max:2000'],
            'vendor_invoice_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ], [
            'license_id.required'        => 'Lisensi wajib dipilih.',
            'amount.required'            => 'Jumlah invoice wajib diisi.',
            'invoice_date.required'      => 'Tanggal invoice wajib diisi.',
            'due_date.required'          => 'Tanggal jatuh tempo wajib diisi.',
            'due_date.after_or_equal'    => 'Tanggal jatuh tempo harus setelah tanggal invoice.',
        ]);

        $license = License::with('vendor')->findOrFail($validated['license_id']);
        
        $filePath = null;
        if ($request->hasFile('vendor_invoice_file')) {
            $filePath = $request->file('vendor_invoice_file')->store('invoices/docs', 'public');
        }

        $invoice = Invoice::create([
            'invoice_number' => Invoice::generateInvoiceNumber(),
            'license_id'     => $validated['license_id'],
            'vendor_id'      => $license->vendor_id,
            'amount'         => $validated['amount'],
            'tax_amount'     => 0,
            'total_amount'   => $validated['amount'],
            'invoice_date'   => $validated['invoice_date'],
            'due_date'       => $validated['due_date'],
            'status'         => 'unpaid',
            'file_path'      => $filePath,
            'notes'          => $validated['notes'] ?? null,
            'created_by'     => auth()->id(),
        ]);

        // Auto-update master price of license unconditionally
        $oldCost = $license->cost;
        if ($oldCost != $validated['amount']) {
            $license->update(['cost' => $validated['amount']]);
            AuditLog::log('updated', 'License', $license->id, ['cost' => $oldCost], ['cost' => $validated['amount'], 'reason' => 'Auto-updated from invoice']);
        }

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
            'status' => ['required', 'in:unpaid,paid,overdue'],
        ]);

        $oldStatus = $invoice->status;
        $invoice->update(['status' => $validated['status']]);

        // Auto-renewal for billing cycle when marked as paid
        if ($validated['status'] === 'paid' && $oldStatus !== 'paid') {
            if ($invoice->license) {
                $invoice->license->renewExpiryDate();
            }
        }

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
