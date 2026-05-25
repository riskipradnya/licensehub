<x-app-layout>
    <x-slot name="title">Manajemen Kategori Lisensi</x-slot>

    <!-- Header Section -->
    <div class="mb-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold" style="color: var(--color-text-primary);">Manajemen Kategori Lisensi</h1>
            <p class="text-sm mt-1" style="color: var(--color-text-secondary);">Kelola data master kategori untuk lisensi IT Anda.</p>
        </div>
        <button onclick="document.getElementById('createCategoryModal').showModal()" class="btn btn-primary">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Kategori
        </button>
    </div>

    <!-- Alert Notifications (Toast) -->
    @if(session('success'))
        <div x-data="{ show: true }" 
             x-init="setTimeout(() => show = false, 4000)" 
             x-show="show" 
             x-transition.duration.500ms
             class="fixed top-6 right-6 z-[9999] flex items-center p-4 mb-4 text-green-800 rounded-lg bg-green-50 border border-green-200 shadow-xl" 
             role="alert">
            <svg class="flex-shrink-0 w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
            <div class="font-medium">{{ session('success') }}</div>
            <button @click="show = false" class="ml-auto -mx-1.5 -my-1.5 bg-green-50 text-green-500 rounded-lg focus:ring-2 focus:ring-green-400 p-1.5 hover:bg-green-200 inline-flex h-8 w-8">
                <span class="sr-only">Close</span>
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div x-data="{ show: true }" 
             x-init="setTimeout(() => show = false, 5000)" 
             x-show="show" 
             x-transition.duration.500ms
             class="fixed top-6 right-6 z-[9999] flex items-center p-4 mb-4 text-red-800 rounded-lg bg-red-50 border border-red-200 shadow-xl" 
             role="alert">
            <svg class="flex-shrink-0 w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
            <div class="font-medium">{{ session('error') }}</div>
            <button @click="show = false" class="ml-auto -mx-1.5 -my-1.5 bg-red-50 text-red-500 rounded-lg focus:ring-2 focus:ring-red-400 p-1.5 hover:bg-red-200 inline-flex h-8 w-8">
                <span class="sr-only">Close</span>
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
            </button>
        </div>
    @endif

    <!-- Data Table -->
    <div class="card p-0 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="w-16 text-center">No</th>
                        <th>Nama Kategori</th>
                        <th>Deskripsi</th>
                        <th class="text-center">Total Lisensi</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $index => $category)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="font-medium text-gray-900">{{ $category->name }}</td>
                            <td class="text-gray-600 truncate max-w-xs" title="{{ $category->description }}">
                                {{ $category->description ?? '-' }}
                            </td>
                            <td class="text-center">
                                <span class="badge badge--info px-3 py-1 text-sm font-semibold rounded-full">
                                    {{ $category->licenses_count }}
                                </span>
                            </td>
                            <td>
                                <div class="flex items-center justify-center gap-2">
                                    <!-- Edit Button (Alpine Modal Trigger) -->
                                    <button 
                                        type="button" 
                                        onclick="openEditModal({{ $category->id }}, '{{ addslashes($category->name) }}', '{{ addslashes($category->description) }}')"
                                        class="btn btn-secondary text-xs py-1 px-3" 
                                        title="Edit Kategori">
                                        Edit
                                    </button>

                                    <!-- Delete Button with safe prompt -->
                                    <form action="{{ route('licenses.categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini? Tindakan ini tidak dapat dibatalkan.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-ghost text-red-500 p-1" title="Hapus Kategori">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-8 text-gray-500 font-medium">Belum ada data kategori.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Create Category Modal (Native HTML5 Dialog) -->
    <dialog id="createCategoryModal" class="p-0 bg-transparent rounded-xl shadow-2xl backdrop:bg-black/70 backdrop:backdrop-blur-none" style="margin: auto;">
        <div class="w-full max-w-2xl bg-white rounded-xl text-left overflow-hidden" style="background: var(--color-card-bg); width: 600px; max-width: 95vw;">
            <form action="{{ route('licenses.categories.store') }}" method="POST">
                @csrf
                <div class="px-6 pt-6 pb-4">
                    <h3 class="text-lg leading-6 font-medium mb-4" style="color: var(--color-text-primary);">Tambah Kategori Baru</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="form-label font-semibold">Nama Kategori <span class="text-red-500">*</span></label>
                            <input type="text" name="name" class="form-input mt-1 w-full" required placeholder="Contoh: Software, Antivirus, OS">
                            @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="form-label font-semibold">Deskripsi</label>
                            <textarea name="description" class="form-input mt-1 w-full h-24" placeholder="Keterangan kategori..."></textarea>
                            @error('description') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 flex justify-end gap-3" style="background: rgba(0,0,0,0.02); border-top: 1px solid var(--color-border);">
                    <button type="button" onclick="document.getElementById('createCategoryModal').close()" class="btn btn-secondary">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Kategori</button>
                </div>
            </form>
        </div>
    </dialog>

    <!-- Edit Category Modal (Native HTML5 Dialog + Alpine for state) -->
    <dialog id="editCategoryModal" class="p-0 bg-transparent rounded-xl shadow-2xl backdrop:bg-black/70 backdrop:backdrop-blur-none" style="margin: auto;">
        <div class="w-full max-w-2xl bg-white rounded-xl text-left overflow-hidden" style="background: var(--color-card-bg); width: 600px; max-width: 95vw;">
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="px-6 pt-6 pb-4">
                    <h3 class="text-lg leading-6 font-medium mb-4" style="color: var(--color-text-primary);">Edit Kategori</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="form-label font-semibold">Nama Kategori <span class="text-red-500">*</span></label>
                            <input type="text" id="editName" name="name" class="form-input mt-1 w-full" required>
                        </div>
                        <div>
                            <label class="form-label font-semibold">Deskripsi</label>
                            <textarea id="editDescription" name="description" class="form-input mt-1 w-full h-24"></textarea>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 flex justify-end gap-3" style="background: rgba(0,0,0,0.02); border-top: 1px solid var(--color-border);">
                    <button type="button" onclick="document.getElementById('editCategoryModal').close()" class="btn btn-secondary">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </dialog>

    <!-- Script to handle dynamic edit modal injection -->
    <script>
        function openEditModal(id, name, description) {
            // Set form action route dynamically
            const form = document.getElementById('editForm');
            form.action = `/licenses/categories/${id}`;

            // Populate inputs
            document.getElementById('editName').value = name;
            document.getElementById('editDescription').value = description;

            // Open modal
            document.getElementById('editCategoryModal').showModal();
        }
    </script>
</x-app-layout>
