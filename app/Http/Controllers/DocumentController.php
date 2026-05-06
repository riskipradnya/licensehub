<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Document;
use App\Models\License;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    /**
     * Display a listing of documents with filters.
     */
    public function index(Request $request): View
    {
        $query = Document::with(['license', 'uploader'])->latest();

        // Search filter
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('file_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('license', fn($q) => $q->where('name', 'like', "%{$search}%"));
            });
        }

        // Type filter
        if ($type = $request->input('type')) {
            $query->where('file_type', $type);
        }

        $documents = $query->paginate(15)->withQueryString();
        $licenses  = License::orderBy('name')->get();

        return view('documents.index', compact('documents', 'licenses'));
    }

    /**
     * Store a newly uploaded document.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'license_id'  => ['required', 'exists:licenses,id'],
            'file'        => ['required', 'file', 'max:10240', 'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png'],
            'description' => ['nullable', 'string', 'max:255'],
        ], [
            'license_id.required' => 'Lisensi terkait wajib dipilih.',
            'file.required'       => 'File dokumen wajib diupload.',
            'file.max'            => 'Ukuran file maksimal 10 MB.',
            'file.mimes'          => 'Format file harus: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG.',
        ]);

        $file = $request->file('file');

        // Store to storage/app/public/documents/
        $path = $file->store('documents', 'public');

        $document = Document::create([
            'license_id'  => $validated['license_id'],
            'uploaded_by' => auth()->id(),
            'file_name'   => $file->getClientOriginalName(),
            'file_path'   => $path,
            'file_size'   => $file->getSize(),
            'file_type'   => $validated['description'] ?? $this->guessDocType($file->getClientOriginalName()),
            'description' => $validated['description'],
        ]);

        AuditLog::log('uploaded', 'Document', $document->id, null, [
            'file_name'  => $document->file_name,
            'license_id' => $document->license_id,
        ]);

        return redirect()
            ->route('documents.index')
            ->with('success', "Dokumen \"{$document->file_name}\" berhasil diupload.");
    }

    /**
     * Download a document.
     */
    public function show(Document $document): StreamedResponse
    {
        if (!Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'File tidak ditemukan.');
        }

        return Storage::disk('public')->download(
            $document->file_path,
            $document->file_name
        );
    }

    /**
     * Delete a document.
     */
    public function destroy(Document $document): RedirectResponse
    {
        $fileName = $document->file_name;

        // Delete file from storage
        Storage::disk('public')->delete($document->file_path);

        $document->delete();

        AuditLog::log('deleted', 'Document', $document->id, ['file_name' => $fileName]);

        return redirect()
            ->route('documents.index')
            ->with('success', "Dokumen \"{$fileName}\" berhasil dihapus.");
    }

    /**
     * Guess document type from filename.
     */
    private function guessDocType(string $filename): string
    {
        $lower = strtolower($filename);

        if (str_contains($lower, 'kontrak') || str_contains($lower, 'contract')) {
            return 'Contract';
        }
        if (str_contains($lower, 'invoice') || str_contains($lower, 'inv')) {
            return 'Invoice';
        }
        if (str_contains($lower, 'cert') || str_contains($lower, 'sertifikat')) {
            return 'Certificate';
        }
        if (str_contains($lower, 'quotation') || str_contains($lower, 'penawaran')) {
            return 'Quotation';
        }

        return 'Other';
    }
}
