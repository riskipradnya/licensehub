<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Document;
use App\Models\License;
use App\Models\Vendor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    public function index(Request $request): View
    {
        // 1. Fetch License Documents
        $licenseDocs = Document::with(['license.vendor', 'uploader'])->get()->map(function ($doc) {
            return [
                'id'            => 'doc_' . $doc->id,
                'file_name'     => $doc->file_name,
                'file_path'     => $doc->file_path,
                'document_type' => $doc->document_type,
                'file_size_formatted' => $doc->file_size_formatted,
                'uploaded_date' => $doc->created_at,
                'uploaded_by'   => $doc->uploader->name ?? '—',
                'related_to'    => $doc->license ? 'License: ' . $doc->license->name : '—',
                'is_vendor_doc' => false,
                'original_id'   => $doc->id,
            ];
        });

        // 2. Fetch Vendor Documents
        $vendors = Vendor::whereNotNull('logo')->orWhereNotNull('msa_file')->orWhereNotNull('sla_file')->get();
        $vendorDocs = collect();

        foreach ($vendors as $vendor) {
            $uploadedByName = auth()->user()->name ?? 'Super Admin';
            if ($vendor->logo) {
                $sizeFormatted = '-';
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($vendor->logo)) {
                    $sizeFormatted = $this->formatFileSize(\Illuminate\Support\Facades\Storage::disk('public')->size($vendor->logo));
                }
                $vendorDocs->push([
                    'id'            => 'vendor_logo_' . $vendor->id,
                    'file_name'     => $vendor->name . ' - Logo',
                    'file_path'     => $vendor->logo,
                    'document_type' => 'Vendor Logo',
                    'file_size_formatted' => $sizeFormatted,
                    'uploaded_date' => $vendor->updated_at,
                    'uploaded_by'   => $uploadedByName,
                    'related_to'    => 'Vendor: ' . $vendor->name,
                    'is_vendor_doc' => true,
                    'original_id'   => $vendor->id,
                    'vendor_field'  => 'logo',
                ]);
            }
            if ($vendor->msa_file) {
                $sizeFormatted = '-';
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($vendor->msa_file)) {
                    $sizeFormatted = $this->formatFileSize(\Illuminate\Support\Facades\Storage::disk('public')->size($vendor->msa_file));
                }
                $vendorDocs->push([
                    'id'            => 'vendor_msa_' . $vendor->id,
                    'file_name'     => $vendor->name . ' - MSA',
                    'file_path'     => $vendor->msa_file,
                    'document_type' => 'Vendor MSA',
                    'file_size_formatted' => $sizeFormatted,
                    'uploaded_date' => $vendor->updated_at,
                    'uploaded_by'   => $uploadedByName,
                    'related_to'    => 'Vendor: ' . $vendor->name,
                    'is_vendor_doc' => true,
                    'original_id'   => $vendor->id,
                    'vendor_field'  => 'msa_file',
                ]);
            }
            if ($vendor->sla_file) {
                $sizeFormatted = '-';
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($vendor->sla_file)) {
                    $sizeFormatted = $this->formatFileSize(\Illuminate\Support\Facades\Storage::disk('public')->size($vendor->sla_file));
                }
                $vendorDocs->push([
                    'id'            => 'vendor_sla_' . $vendor->id,
                    'file_name'     => $vendor->name . ' - SLA',
                    'file_path'     => $vendor->sla_file,
                    'document_type' => 'Vendor SLA',
                    'file_size_formatted' => $sizeFormatted,
                    'uploaded_date' => $vendor->updated_at,
                    'uploaded_by'   => $uploadedByName,
                    'related_to'    => 'Vendor: ' . $vendor->name,
                    'is_vendor_doc' => true,
                    'original_id'   => $vendor->id,
                    'vendor_field'  => 'sla_file',
                ]);
            }
        }

        // 3. Merge Collections
        $allDocs = $licenseDocs->concat($vendorDocs);

        // 4. Extract Filter Options
        $uploaders = \App\Models\User::whereIn('id', Document::select('uploaded_by')->distinct())->pluck('name')->toArray();
        if ($vendorDocs->isNotEmpty()) {
            $uploaders[] = auth()->user()->name ?? 'Super Admin';
        }
        $uploaders = array_unique($uploaders);
        sort($uploaders);

        // 5. Apply Filters
        if ($search = strtolower($request->input('search'))) {
            $allDocs = $allDocs->filter(function ($item) use ($search) {
                return str_contains(strtolower($item['file_name']), $search) ||
                       str_contains(strtolower($item['related_to']), $search);
            });
        }

        if ($type = $request->input('type')) {
            $allDocs = $allDocs->where('document_type', $type);
        }

        if ($uploadedBy = $request->input('uploaded_by')) {
            $allDocs = $allDocs->where('uploaded_by', $uploadedBy);
        }

        // 6. Apply Sorting (Default: uploaded_date DESC)
        $sortColumn = $request->input('sort', 'uploaded_date'); // file_name | uploaded_date
        $sortDirection = $request->input('direction', 'desc'); // asc | desc

        if ($sortColumn === 'file_name') {
            $allDocs = $allDocs->sortBy('file_name', SORT_NATURAL | SORT_FLAG_CASE, $sortDirection === 'desc');
        } else {
            // Default sort by date
            if ($sortDirection === 'desc') {
                $allDocs = $allDocs->sortByDesc('uploaded_date');
            } else {
                $allDocs = $allDocs->sortBy('uploaded_date');
            }
        }

        // 7. Manual Pagination
        $page = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage();
        $perPage = 15;
        $sliced = $allDocs->slice(($page - 1) * $perPage, $perPage)->values();

        $documents = new \Illuminate\Pagination\LengthAwarePaginator(
            $sliced,
            $allDocs->count(),
            $perPage,
            $page,
            ['path' => \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPath(), 'query' => $request->query()]
        );

        return view('documents.index', compact('documents', 'uploaders', 'sortColumn', 'sortDirection'));
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
            'document_type' => $this->guessDocType($file->getClientOriginalName()),
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
     * Delete a vendor document.
     */
    public function destroyVendorDoc(Vendor $vendor, string $field): RedirectResponse
    {
        $validFields = ['logo', 'msa_file', 'sla_file'];

        if (!in_array($field, $validFields)) {
            abort(400, 'Invalid document field.');
        }

        if ($vendor->{$field}) {
            Storage::disk('public')->delete($vendor->{$field});
            $vendor->update([$field => null]);
            
            AuditLog::log('deleted', 'Vendor Document', $vendor->id, [
                'field' => $field,
                'vendor_name' => $vendor->name
            ]);
        }

        return redirect()
            ->route('documents.index')
            ->with('success', "Dokumen vendor berhasil dihapus.");
    }

    /**
     * Guess document type from filename.
     */
    private function guessDocType(string $filename): string
    {
        $lower = strtolower($filename);

        if (str_contains($lower, 'kontrak') || str_contains($lower, 'contract')) {
            return 'contract';
        }
        if (str_contains($lower, 'invoice') || str_contains($lower, 'inv')) {
            return 'invoice';
        }
        if (str_contains($lower, 'cert') || str_contains($lower, 'sertifikat')) {
            return 'certificate';
        }
        if (str_contains($lower, 'quotation') || str_contains($lower, 'penawaran')) {
            return 'quotation';
        }

        return 'other';
    }

    /**
     * Format bytes to KB or MB (mirroring Document model logic).
     */
    private function formatFileSize(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 1) . ' MB';
        }

        return number_format($bytes / 1024, 1) . ' KB';
    }
}
