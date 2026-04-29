@props([
    'name' => 'document',
    'accept' => '.pdf,.doc,.docx,.jpg,.png',
    'maxSize' => '10',
    'multiple' => false,
])

<div x-data="{
        files: [],
        dragover: false,
        handleDrop(e) {
            this.dragover = false;
            this.addFiles(e.dataTransfer.files);
        },
        handleSelect(e) {
            this.addFiles(e.target.files);
        },
        addFiles(fileList) {
            for (let f of fileList) {
                if (f.size > {{ $maxSize }} * 1024 * 1024) {
                    alert('File ' + f.name + ' melebihi batas {{ $maxSize }}MB');
                    continue;
                }
                this.files.push({ name: f.name, size: (f.size / 1024 / 1024).toFixed(2), file: f });
            }
        },
        removeFile(idx) { this.files.splice(idx, 1); },
        formatSize(mb) { return parseFloat(mb) < 1 ? (parseFloat(mb) * 1024).toFixed(0) + ' KB' : mb + ' MB'; }
     }"
     class="space-y-3">

    {{-- DROP ZONE --}}
    <div class="file-upload-zone"
         :class="{ 'dragover': dragover }"
         @dragover.prevent="dragover = true"
         @dragleave="dragover = false"
         @drop.prevent="handleDrop($event)"
         @click="$refs.fileInput.click()">
        <svg class="w-10 h-10 mx-auto mb-3" style="color: var(--color-text-secondary);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                  d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
        </svg>
        <p class="text-sm font-medium" style="color: var(--color-text-primary);">
            Drag & drop file di sini
        </p>
        <p class="text-xs mt-1" style="color: var(--color-text-secondary);">
            atau <span class="underline" style="color: var(--color-primary);">klik untuk browse</span>
            ({{ strtoupper(str_replace('.', '', $accept)) }}, max {{ $maxSize }}MB)
        </p>
        <input type="file" x-ref="fileInput" class="hidden"
               accept="{{ $accept }}" {{ $multiple ? 'multiple' : '' }}
               @change="handleSelect($event)" name="{{ $name }}{{ $multiple ? '[]' : '' }}">
    </div>

    {{-- FILE LIST --}}
    <template x-for="(file, index) in files" :key="index">
        <div class="flex items-center gap-3 p-3 rounded-lg" style="background: var(--color-content-bg); border: 1px solid var(--color-border);">
            <svg class="w-5 h-5 shrink-0" style="color: var(--color-primary);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
            </svg>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium truncate" style="color: var(--color-text-primary);" x-text="file.name"></p>
                <p class="text-xs" style="color: var(--color-text-secondary);" x-text="formatSize(file.size)"></p>
            </div>
            <button @click="removeFile(index)" class="btn-ghost p-1 rounded hover:text-red-500">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </template>
</div>
