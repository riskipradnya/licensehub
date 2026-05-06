<x-app-layout title="Documents" :breadcrumbs="[['label' => 'IT Department', 'url' => '#'], ['label' => 'Documents']]">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold" style="color: var(--color-text-primary);">Document Center</h2>
            <p class="text-sm mt-1" style="color: var(--color-text-secondary);">Semua dokumen kontrak, invoice, dan sertifikat lisensi</p>
        </div>
        <button class="btn btn-primary" @click="$dispatch('open-modal-upload-doc')" id="upload-doc-btn">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
            Upload Document
        </button>
    </div>

    {{-- FILTER --}}
    <div class="card mb-6">
        <form method="GET" action="{{ route('documents.index') }}" class="flex flex-col md:flex-row gap-3">
            <div class="relative flex-1">
                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2" style="color: var(--color-text-secondary);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari dokumen..." class="form-input pl-10" id="doc-search">
            </div>
            <select name="type" class="form-input w-full md:w-48" onchange="this.form.submit()">
                <option value="">All Types</option>
                <option value="Contract" @selected(request('type') === 'Contract')>Contract</option>
                <option value="Invoice" @selected(request('type') === 'Invoice')>Invoice</option>
                <option value="Certificate" @selected(request('type') === 'Certificate')>Certificate</option>
                <option value="Quotation" @selected(request('type') === 'Quotation')>Quotation</option>
                <option value="Other" @selected(request('type') === 'Other')>Other</option>
            </select>
        </form>
    </div>

    {{-- DOCUMENTS TABLE --}}
    <div class="card p-0 overflow-hidden">
        <table class="data-table" id="documents-table">
            <thead>
                <tr><th>Document Name</th><th>Type</th><th>Related License</th><th>Size</th><th>Uploaded</th><th>Uploaded By</th><th class="text-center">Actions</th></tr>
            </thead>
            <tbody>
                @forelse($documents as $doc)
                <tr>
                    <td class="font-medium">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 shrink-0" style="color: var(--color-status-danger);" fill="currentColor" viewBox="0 0 24 24"><path d="M7 18h10v-2H7v2zM7 14h10v-2H7v2zM7 10h6V8H7v2zm-2 8V4h14v14H5zm0 2h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2z"/></svg>
                            {{ $doc->file_name }}
                        </div>
                    </td>
                    <td><span class="badge badge--info text-[10px]">{{ $doc->file_type }}</span></td>
                    <td>
                        @if($doc->license)
                            <a href="{{ route('licenses.show', $doc->license) }}" class="hover:underline" style="color: var(--color-primary);">{{ $doc->license->name }}</a>
                        @else
                            —
                        @endif
                    </td>
                    <td>{{ $doc->file_size_formatted }}</td>
                    <td>{{ $doc->created_at->format('d M Y') }}</td>
                    <td>{{ $doc->uploader->name ?? '—' }}</td>
                    <td class="text-center">
                        <div class="flex items-center justify-center gap-1">
                            <a href="{{ route('documents.show', $doc) }}" class="btn-ghost p-1.5 rounded-lg" title="Download">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            </a>
                            <form method="POST" action="{{ route('documents.destroy', $doc) }}" class="inline" onsubmit="return confirm('Hapus dokumen ini?')">
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
                    <td colspan="7" class="text-center py-8">
                        <p class="text-sm" style="color: var(--color-text-secondary);">Belum ada dokumen. Upload dokumen pertama Anda.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($documents->hasPages())
        <div class="px-6 py-3 flex items-center justify-between" style="border-top: 1px solid var(--color-border);">
            <span class="text-xs" style="color: var(--color-text-secondary);">
                Showing {{ $documents->firstItem() }}–{{ $documents->lastItem() }} of {{ $documents->total() }}
            </span>
            <div>{{ $documents->links('pagination::tailwind') }}</div>
        </div>
        @endif
    </div>

    {{-- UPLOAD MODAL --}}
    <x-modal id="upload-doc" title="Upload Document" maxWidth="md">
        <form method="POST" action="{{ route('documents.store') }}" enctype="multipart/form-data" id="upload-doc-form">
            @csrf
            <div class="mb-4">
                <label class="form-label">Related License <span style="color: var(--color-status-danger);">*</span></label>
                <select name="license_id" class="form-input" required>
                    <option value="" disabled selected>Pilih Lisensi...</option>
                    @foreach($licenses as $license)
                        <option value="{{ $license->id }}">{{ $license->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="form-label">Description</label>
                <input type="text" name="description" class="form-input" placeholder="e.g. Kontrak tahunan 2026">
            </div>
            <div class="mb-4">
                <label class="form-label">File <span style="color: var(--color-status-danger);">*</span></label>
                <input type="file" name="file" class="form-input" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png" required>
                <p class="text-xs mt-1" style="color: var(--color-text-secondary);">Max 10 MB • PDF, DOC, DOCX, XLS, XLSX, JPG, PNG</p>
            </div>
            <x-slot:footer>
                <button type="button" @click="hide()" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary">📤 Upload</button>
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
