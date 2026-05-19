<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Category;
use App\Models\License;
use App\Models\Vendor;
use App\Models\Document;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class LicenseController extends Controller
{
    /**
     * Display a listing of licenses with filters & pagination.
     */
    public function index(Request $request): View
    {
        $query = License::with(['vendor', 'category'])
            ->search($request->input('search'))
            ->filterCategory($request->input('category'))
            ->filterStatus($request->input('status'))
            ->orderByRaw("FIELD(status, 'expiring', 'active', 'expired', 'cancelled')")
            ->orderBy('expiry_date');

        $licenses  = $query->paginate(10)->withQueryString();
        $categories = Category::ordered()->get();

        return view('licenses.index', compact('licenses', 'categories'));
    }

    /**
     * Show the form for creating a new license.
     */
    public function create(): View
    {
        $categories = Category::ordered()->get();
        $vendors    = Vendor::active()->ordered()->get();

        return view('licenses.create', compact('categories', 'vendors'));
    }

    /**
     * Store a newly created license.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'vendor_id'     => ['required', 'exists:vendors,id'],
            'category_id'   => ['required', 'exists:categories,id'],
            'type'          => ['required', 'in:subscription,perpetual'],
            'serial_key'    => ['nullable', 'string', 'max:255'],
            'start_date'    => ['required', 'date'],
            'expiry_date'   => ['nullable', 'date', 'after_or_equal:start_date'],
            'seats'         => ['nullable', 'integer', 'min:1'],
            'cost'          => ['required', 'numeric', 'min:0'],
            'billing_cycle' => ['required', 'in:monthly,quarterly,yearly,one_time'],
            'notes'         => ['nullable', 'string', 'max:5000'],
            'documents'        => ['nullable', 'array'],
            'documents.*.file' => ['required_with:documents', 'file', 'mimes:pdf,docx,zip,png,jpg,jpeg', 'max:10240'],
            'documents.*.type' => ['required_with:documents', 'in:contract,invoice,certificate,quotation,other'],
        ], [
            'name.required'             => 'Nama lisensi wajib diisi.',
            'vendor_id.required'        => 'Vendor wajib dipilih.',
            'vendor_id.exists'          => 'Vendor tidak ditemukan.',
            'category_id.required'      => 'Kategori wajib dipilih.',
            'category_id.exists'        => 'Kategori tidak ditemukan.',
            'type.required'             => 'Tipe lisensi wajib dipilih.',
            'start_date.required'       => 'Tanggal mulai wajib diisi.',
            'expiry_date.after_or_equal' => 'Tanggal kedaluwarsa harus setelah tanggal mulai.',
            'cost.required'             => 'Biaya lisensi wajib diisi.',
            'cost.min'                  => 'Biaya tidak boleh negatif.',
        ]);

        // Set created_by
        $validated['created_by'] = auth()->id();

        // Auto-compute status based on dates
        $validated['status'] = $this->computeStatus(
            $validated['type'],
            $validated['expiry_date'] ?? null
        );

        $license = License::create($validated);

        // Handle Documents Upload
        if ($request->has('documents')) {
            foreach ($request->file('documents') as $index => $docData) {
                if (isset($docData['file']) && $docData['file']->isValid()) {
                    $file = $docData['file'];
                    $type = $request->input("documents.{$index}.type") ?? 'other';
                    $path = $file->store('licenses/documents', 'public');
                    
                    Document::create([
                        'license_id'    => $license->id,
                        'uploaded_by'   => auth()->id(),
                        'file_name'     => $file->getClientOriginalName(),
                        'file_path'     => $path,
                        'file_size'     => $file->getSize(),
                        'document_type' => $type,
                    ]);
                }
            }
        }

        AuditLog::log('created', 'License', $license->id, null, $validated);

        return redirect()
            ->route('licenses.show', $license)
            ->with('success', "Lisensi \"{$license->name}\" berhasil ditambahkan.");
    }

    /**
     * Display the specified license with all details.
     */
    public function show(License $license): View
    {
        $license->load([
            'vendor',
            'category',
            'creator',
            'documents' => fn($q) => $q->latest()->limit(10),
            'payments'  => fn($q) => $q->latest()->limit(10),
        ]);

        // Recent audit log entries for this license
        $auditLogs = AuditLog::forModel('License', $license->id)
            ->with('user')
            ->latest()
            ->limit(5)
            ->get();

        return view('licenses.show', compact('license', 'auditLogs'));
    }

    /**
     * Show the form for editing the specified license.
     */
    public function edit(License $license): View
    {
        $license->load(['documents']);
        $categories = Category::ordered()->get();
        $vendors    = Vendor::active()->ordered()->get();

        return view('licenses.edit', compact('license', 'categories', 'vendors'));
    }

    /**
     * Update the specified license.
     */
    public function update(Request $request, License $license): RedirectResponse
    {
        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'vendor_id'     => ['required', 'exists:vendors,id'],
            'category_id'   => ['required', 'exists:categories,id'],
            'type'          => ['required', 'in:subscription,perpetual'],
            'serial_key'    => ['nullable', 'string', 'max:255'],
            'start_date'    => ['required', 'date'],
            'expiry_date'   => ['nullable', 'date', 'after_or_equal:start_date'],
            'seats'         => ['nullable', 'integer', 'min:1'],
            'cost'          => ['required', 'numeric', 'min:0'],
            'billing_cycle' => ['required', 'in:monthly,quarterly,yearly,one_time'],
            'notes'         => ['nullable', 'string', 'max:5000'],
            'documents'        => ['nullable', 'array'],
            'documents.*.file' => ['required_with:documents', 'file', 'mimes:pdf,docx,zip,png,jpg,jpeg', 'max:10240'],
            'documents.*.type' => ['required_with:documents', 'in:contract,invoice,certificate,quotation,other'],
        ], [
            'name.required'             => 'Nama lisensi wajib diisi.',
            'vendor_id.required'        => 'Vendor wajib dipilih.',
            'category_id.required'      => 'Kategori wajib dipilih.',
            'start_date.required'       => 'Tanggal mulai wajib diisi.',
            'expiry_date.after_or_equal' => 'Tanggal kedaluwarsa harus setelah tanggal mulai.',
            'cost.required'             => 'Biaya lisensi wajib diisi.',
        ]);

        // Hitung status berdasarkan expiry_date yang akan disimpan.
        // computeStatus() menghitung murni dari tanggal — jika expiry_date
        // di masa depan (>30 hari), hasilnya selalu 'active'.
        // Ini aman karena renewLicense() sudah menulis expiry_date yang benar ke DB.
        $validated['status'] = $this->computeStatus(
            $validated['type'],
            $validated['expiry_date'] ?? null
        );

        $oldValues = $license->only(array_keys($validated));

        $license->update($validated);

        // Handle Documents Upload
        if ($request->has('documents')) {
            foreach ($request->file('documents') as $index => $docData) {
                if (isset($docData['file']) && $docData['file']->isValid()) {
                    $file = $docData['file'];
                    $type = $request->input("documents.{$index}.type") ?? 'other';
                    $path = $file->store('licenses/documents', 'public');
                    
                    Document::create([
                        'license_id'    => $license->id,
                        'uploaded_by'   => auth()->id(),
                        'file_name'     => $file->getClientOriginalName(),
                        'file_path'     => $path,
                        'file_size'     => $file->getSize(),
                        'document_type' => $type,
                    ]);
                }
            }
        }

        AuditLog::log('updated', 'License', $license->id, $oldValues, $validated);

        return redirect()
            ->route('licenses.show', $license)
            ->with('success', "Lisensi \"{$license->name}\" berhasil diperbarui.");
    }

    /**
     * Soft-delete the specified license.
     */
    public function destroy(License $license): RedirectResponse
    {
        $name = $license->name;
        $license->delete();

        AuditLog::log('deleted', 'License', $license->id, ['name' => $name]);

        return redirect()
            ->route('licenses.index')
            ->with('success', "Lisensi \"{$name}\" berhasil dihapus.");
    }

    /**
     * Auto-compute status based on type and expiry date.
     *
     * Selalu menghitung berdasarkan expiry_date yang akan disimpan,
     * bukan status lama — sehingga form edit selalu konsisten dengan data DB.
     * Jika lisensi baru saja diperpanjang (expiry_date jauh di masa depan),
     * status akan otomatis dihitung 'active' dari perhitungan hari.
     */
    private function computeStatus(string $type, ?string $expiryDate): string
    {
        if ($type === 'perpetual' && !$expiryDate) {
            return 'active';
        }

        if (!$expiryDate) {
            return 'active';
        }

        $expiry   = \Carbon\Carbon::parse($expiryDate);
        $daysLeft = (int) now()->diffInDays($expiry, false);

        if ($daysLeft <= 0) {
            return 'expired';
        }

        if ($daysLeft <= 30) {
            return 'expiring';
        }

        return 'active';
    }
}
