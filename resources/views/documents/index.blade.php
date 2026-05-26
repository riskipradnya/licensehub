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
        <form method="GET" action="{{ route('documents.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 w-full">
            <div class="relative md:col-span-2">
                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2" style="color: var(--color-text-secondary);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari dokumen..." class="form-input pl-10 w-full" id="doc-search">
            </div>
            <div class="md:col-span-1">
                <select name="type" class="form-input w-full" onchange="this.form.submit()">
                <option value="">All Types</option>
                <optgroup label="License Documents">
                    <option value="contract" @selected(request('type') === 'contract')>Contract</option>
                    <option value="invoice" @selected(request('type') === 'invoice')>Invoice</option>
                    <option value="certificate" @selected(request('type') === 'certificate')>Certificate</option>
                    <option value="quotation" @selected(request('type') === 'quotation')>Quotation</option>
                    <option value="other" @selected(request('type') === 'other')>Other</option>
                </optgroup>
                <optgroup label="Vendor Legal Docs">
                    <option value="Vendor Logo" @selected(request('type') === 'Vendor Logo')>Vendor Logo</option>
                    <option value="Vendor MSA" @selected(request('type') === 'Vendor MSA')>Vendor MSA</option>
                    <option value="Vendor SLA" @selected(request('type') === 'Vendor SLA')>Vendor SLA</option>
                </optgroup>1
            </select>
            </div>
            <div class="md:col-span-1">
                <select name="uploaded_by" class="form-input w-full" onchange="this.form.submit()">
                    <option value="">All Uploaders</option>
                @foreach($uploaders as $uploader)
                    <option value="{{ $uploader }}" @selected(request('uploaded_by') == $uploader)>{{ $uploader }}</option>
                @endforeach
                </select>
            </div>
        </form>
    </div>

    {{-- DOCUMENTS TABLE --}}
    <div class="card p-0 overflow-hidden">
        <table class="data-table" id="documents-table">
            <thead>
                <tr>
                    <th>
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'file_name', 'direction' => (request('sort') === 'file_name' && request('direction') === 'asc') ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-indigo-600 transition-colors">
                            Document Name
                            @if(request('sort') === 'file_name')
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ request('direction') === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path>
                                </svg>
                            @else
                                <svg class="w-4 h-4 text-gray-400 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path></svg>
                            @endif
                        </a>
                    </th>
                    <th>Type</th>
                    <th>Related To</th>
                    <th>Size</th>
                    <th>
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'uploaded_date', 'direction' => (request('sort', 'uploaded_date') === 'uploaded_date' && request('direction', 'desc') === 'desc') ? 'asc' : 'desc']) }}" class="flex items-center gap-1 hover:text-indigo-600 transition-colors">
                            Uploaded Date
                            @if(request('sort', 'uploaded_date') === 'uploaded_date')
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ request('direction', 'desc') === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path>
                                </svg>
                            @else
                                <svg class="w-4 h-4 text-gray-400 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path></svg>
                            @endif
                        </a>
                    </th>
                    <th>Uploaded By</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($documents as $doc)
                <tr>
                    <td class="font-medium">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 shrink-0" style="color: var(--color-status-danger);" fill="currentColor" viewBox="0 0 24 24"><path d="M7 18h10v-2H7v2zM7 14h10v-2H7v2zM7 10h6V8H7v2zm-2 8V4h14v14H5zm0 2h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2z"/></svg>
                            {{ $doc['file_name'] }}
                        </div>
                    </td>
                    <td>
                        @if($doc['is_vendor_doc'])
                            <span class="w-28 inline-block text-center text-[10px] uppercase font-semibold border border-orange-300 bg-orange-100 text-orange-800 rounded-full py-0.5">{{ $doc['document_type'] }}</span>
                        @else
                            <span class="w-28 inline-block text-center text-[10px] uppercase font-semibold border border-indigo-300 bg-indigo-100 text-indigo-800 rounded-full py-0.5">{{ $doc['document_type'] }}</span>
                        @endif
                    </td>
                    <td class="text-xs font-medium">{{ $doc['related_to'] }}</td>
                    <td class="text-xs">{{ $doc['file_size_formatted'] }}</td>
                    <td class="text-xs">{{ \Carbon\Carbon::parse($doc['uploaded_date'])->format('d M Y') }}</td>
                    <td class="text-xs">{{ $doc['uploaded_by'] }}</td>
                    <td class="text-center">
                        <div class="flex items-center justify-center gap-1">
                            <a href="{{ Storage::url($doc['file_path']) }}" target="_blank" class="btn-ghost p-1.5 rounded-lg" title="Download / View">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            </a>
                            @if($doc['is_vendor_doc'])
                            <form method="POST" action="{{ route('documents.destroyVendorDoc', ['vendor' => $doc['original_id'], 'field' => $doc['vendor_field']]) }}" class="inline" onsubmit="return confirm('Hapus dokumen vendor ini secara permanen?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-ghost p-1.5 rounded-lg hover:text-red-500" title="Delete Vendor Doc">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                            @else
                            <form method="POST" action="{{ route('documents.destroy', $doc['original_id']) }}" class="inline" onsubmit="return confirm('Hapus dokumen ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-ghost p-1.5 rounded-lg hover:text-red-500" title="Delete">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                            @endif
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
                <label class="form-label">Tipe Dokumen <span style="color: var(--color-status-danger);">*</span></label>
                <select name="document_type" class="form-input" required>
                    <option value="contract">Contract</option>
                    <option value="invoice">Invoice</option>
                    <option value="certificate">Certificate</option>
                    <option value="quotation">Quotation</option>
                    <option value="other" selected>Other</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="form-label">Related License <span style="color: var(--color-status-danger);">*</span></label>
                <input type="number" name="license_id" class="form-input" placeholder="ID Lisensi" required>
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
