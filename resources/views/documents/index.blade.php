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
        <div class="flex flex-col md:flex-row gap-3">
            <div class="relative flex-1">
                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2" style="color: var(--color-text-secondary);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" placeholder="Cari dokumen..." class="form-input pl-10" id="doc-search">
            </div>
            <select class="form-input w-full md:w-48">
                <option value="">All Types</option>
                <option>Contract</option>
                <option>Invoice</option>
                <option>Certificate</option>
                <option>Quotation</option>
            </select>
        </div>
    </div>

    {{-- DOCUMENTS TABLE --}}
    <div class="card p-0 overflow-hidden">
        <table class="data-table" id="documents-table">
            <thead>
                <tr><th>Document Name</th><th>Type</th><th>Related License</th><th>Size</th><th>Uploaded</th><th>Uploaded By</th><th class="text-center">Actions</th></tr>
            </thead>
            <tbody>
                @php
                $docs = [
                    ['name' => 'Kontrak_MS365_2025.pdf', 'type' => 'Contract', 'license' => 'Microsoft 365', 'size' => '2.1 MB', 'date' => '28 Apr 2025', 'by' => 'Admin'],
                    ['name' => 'Invoice_Oracle_2026.pdf', 'type' => 'Invoice', 'license' => 'Oracle Database', 'size' => '1.4 MB', 'date' => '15 Mar 2026', 'by' => 'IT Staff'],
                    ['name' => 'Cert_Kaspersky_2025.pdf', 'type' => 'Certificate', 'license' => 'Kaspersky Endpoint', 'size' => '890 KB', 'date' => '10 Jan 2025', 'by' => 'Admin'],
                    ['name' => 'Quotation_Adobe_2026.pdf', 'type' => 'Quotation', 'license' => 'Adobe CC', 'size' => '3.2 MB', 'date' => '01 Mar 2026', 'by' => 'IT Staff'],
                    ['name' => 'Invoice_VMware_2025.pdf', 'type' => 'Invoice', 'license' => 'VMware vSphere', 'size' => '1.8 MB', 'date' => '20 Feb 2025', 'by' => 'Admin'],
                ];
                @endphp
                @foreach($docs as $doc)
                <tr>
                    <td class="font-medium flex items-center gap-2">
                        <svg class="w-4 h-4 shrink-0" style="color: var(--color-status-danger);" fill="currentColor" viewBox="0 0 24 24"><path d="M7 18h10v-2H7v2zM7 14h10v-2H7v2zM7 10h6V8H7v2zm-2 8V4h14v14H5zm0 2h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2z"/></svg>
                        {{ $doc['name'] }}
                    </td>
                    <td><span class="badge badge--info text-[10px]">{{ $doc['type'] }}</span></td>
                    <td>{{ $doc['license'] }}</td>
                    <td>{{ $doc['size'] }}</td>
                    <td>{{ $doc['date'] }}</td>
                    <td>{{ $doc['by'] }}</td>
                    <td class="text-center">
                        <div class="flex items-center justify-center gap-1">
                            <button class="btn-ghost p-1.5 rounded-lg" title="View"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></button>
                            <button class="btn-ghost p-1.5 rounded-lg" title="Download"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg></button>
                            <button class="btn-ghost p-1.5 rounded-lg hover:text-red-500" title="Delete"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- UPLOAD MODAL --}}
    <x-modal id="upload-doc" title="Upload Document" maxWidth="md">
        <form id="upload-doc-form">
            <div class="mb-4">
                <label class="form-label">Related License</label>
                <select class="form-input"><option>Pilih Lisensi...</option><option>Microsoft 365</option><option>Oracle Database</option></select>
            </div>
            <div class="mb-4">
                <label class="form-label">Document Type</label>
                <select class="form-input"><option>Contract</option><option>Invoice</option><option>Certificate</option><option>Quotation</option></select>
            </div>
            <x-file-upload name="file" accept=".pdf,.doc,.docx" maxSize="10" />
            <x-slot:footer>
                <button type="button" @click="hide()" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary">Upload</button>
            </x-slot:footer>
        </form>
    </x-modal>

</x-app-layout>
