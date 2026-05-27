<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\License;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    /**
     * Display active/pending payments (Process Payment page).
     */
    public function index(Request $request): View
    {
        // Tampilkan daftar lisensi yang butuh pembayaran (expiring/expired),
        // kecuali yang sudah memiliki transaksi aktif (pending = sedang diproses,
        // paid = sudah lunas) agar user tidak melakukan double payment.
        $licenses = License::with(['vendor', 'category'])
            ->where('expiry_date', '<=', now()->addDays(31))
            ->whereNotIn('status', ['paid', 'cancelled'])
            // ->whereDoesntHave('payments', function ($query) {
            //     $query->whereIn('status', ['pending', 'paid']);
            // })
            ->orderBy('expiry_date', 'asc')
            ->paginate(15);

        return view('finance.payments', compact('licenses'));
    }

    /**
     * Show the renewal/payment page for a specific license.
     */
    public function renew(License $license): View
    {
        $license->load(['vendor', 'category']);

        return view('finance.renew', compact('license'));
    }

    /**
     * Store a new payment request.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'license_id'     => ['required', 'exists:licenses,id'],
            'amount'         => ['required', 'numeric', 'min:1'],
            'payment_date'   => ['required', 'date'],
            'payment_method' => ['nullable', 'in:transfer,credit_card,e_wallet,cash,transfer'],
            'notes'          => ['nullable', 'string', 'max:2000'],
        ], [
            'license_id.required'   => 'Lisensi wajib dipilih.',
            'amount.required'       => 'Jumlah pembayaran wajib diisi.',
            'payment_date.required' => 'Tanggal pembayaran wajib diisi.',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['status'] = $request->input('status', 'pending');

        $payment = Payment::create($validated);



        return redirect()
            ->route('payments.index')
            ->with('success', 'Payment request berhasil dibuat.');
    }

    /**
     * Approve a pending payment (Finance Manager only).
     */
    public function approve(Payment $payment): RedirectResponse
    {
        if (!$payment->isPending()) {
            return back()->with('error', 'Payment ini sudah diproses.');
        }

        $oldStatus = $payment->status;
        $payment->approve(auth()->user());



        return back()->with('success', "Payment untuk \"{$payment->license->name}\" berhasil diapprove.");
    }

    /**
     * Mark an approved payment as paid.
     */
    public function markPaid(Request $request, Payment $payment): RedirectResponse
    {
        if ($payment->status !== 'approved') {
            return back()->with('error', 'Payment harus diapprove terlebih dahulu.');
        }

        $validated = $request->validate([
            'payment_method'   => ['required', 'in:transfer,credit_card,e_wallet,cash'],
            'reference_number' => ['nullable', 'string', 'max:100'],
        ]);

        $payment->update([
            'status'           => 'paid',
            'payment_method'   => $validated['payment_method'],
            'reference_number' => $validated['reference_number'] ?? null,
        ]);

        // Generate PDF Receipt and save to Documents
        try {
            $payment->load(['license.vendor', 'license.category']);
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('finance.receipt_pdf', compact('payment'));
            
            $fileName = 'receipt_' . ($payment->reference_number ?? $payment->id) . '_' . time() . '.pdf';
            $filePath = 'payments/receipts/' . $fileName;
            
            \Illuminate\Support\Facades\Storage::disk('public')->put($filePath, $pdf->output());

            \App\Models\Document::create([
                'license_id'    => $payment->license_id,
                'uploaded_by'   => auth()->id(),
                'file_name'     => $fileName,
                'file_path'     => $filePath,
                'file_size'     => \Illuminate\Support\Facades\Storage::disk('public')->size($filePath),
                'document_type' => 'Receipt',
                'description'   => 'Auto-generated Receipt: ' . ($payment->reference_number ?? $payment->id),
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to generate/save receipt PDF: ' . $e->getMessage());
        }

        return back()->with('success', "Payment berhasil dikonfirmasi sebagai Paid.");
    }

    /**
     * Reject a pending payment.
     */
    public function reject(Payment $payment): RedirectResponse
    {
        if (!$payment->isPending()) {
            return back()->with('error', 'Payment ini sudah diproses.');
        }

        $payment->update(['status' => 'rejected']);



        return back()->with('success', "Payment berhasil ditolak.");
    }

    /**
     * Payment History page (all completed/past payments).
     */
    public function history(Request $request): View
    {
        $query = Payment::with(['license.vendor', 'creator', 'approver'])
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
            ->orderBy('created_at', 'desc');

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }
        if ($method = $request->input('method')) {
            $query->where('payment_method', $method);
        }

        $payments = $query->paginate(15)->withQueryString();

        return view('finance.payment-history', compact('payments'));
    }

    /**
     * Download Payment Receipt as PDF.
     */
    public function downloadReceipt(Payment $payment)
    {
        $payment->load(['license.vendor', 'license.category']);
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('finance.receipt_pdf', compact('payment'));
        
        return $pdf->download('Receipt-'.$payment->reference_number.'.pdf');
    }
}
