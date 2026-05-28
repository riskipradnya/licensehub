<x-app-layout>
    <x-slot name="title">User & Role Management</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white" style="color: var(--color-text-primary);">Manajemen Pengguna</h1>
                <p class="text-sm mt-1 text-gray-500" style="color: var(--color-text-secondary);">Kelola akun dan hak akses internal perusahaan.</p>
            </div>
            <button onclick="document.getElementById('createUserModal').showModal()" class="btn btn-primary shadow-lg hover:shadow-xl transition-all">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Pengguna
            </button>
        </div>

        <!-- Alert Notifications -->
        @if(session('success'))
            <div class="mb-4 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 shadow-sm flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-4 p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 shadow-sm flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                {{ session('error') }}
            </div>
        @endif

        <!-- Data Table -->
        <div class="card p-0 overflow-hidden shadow-xl rounded-xl border border-gray-200" style="background: var(--color-card-bg);">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-gray-50 border-b border-gray-200" style="background: rgba(0,0,0,0.02);">
                        <tr>
                            <th class="px-6 py-4 font-semibold text-center w-16" style="color: var(--color-text-secondary);">No</th>
                            <th class="px-6 py-4 font-semibold" style="color: var(--color-text-secondary);">Nama</th>
                            <th class="px-6 py-4 font-semibold" style="color: var(--color-text-secondary);">Username</th>
                            <th class="px-6 py-4 font-semibold text-center" style="color: var(--color-text-secondary);">Role</th>
                            <th class="px-6 py-4 font-semibold text-center" style="color: var(--color-text-secondary);">Terdaftar</th>
                            <th class="px-6 py-4 font-semibold text-center" style="color: var(--color-text-secondary);">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($users as $index => $user)
                            @php
                                // Role Badge Mapping
                                $badgeClass = match($user->role) {
                                    'super_admin'  => 'bg-purple-100 text-purple-800 border-purple-200',
                                    'it_team'      => 'bg-blue-100 text-blue-800 border-blue-200',
                                    'finance_team' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                    default        => 'bg-gray-100 text-gray-800 border-gray-200',
                                };
                                $roleLabel = ucwords(str_replace('_', ' ', $user->role));
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-slate-800/50 transition-colors border-b border-gray-100 dark:border-slate-700/50">
                                <td class="px-6 py-4 text-center text-gray-500">{{ $users->firstItem() + $index }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full overflow-hidden border border-gray-200 bg-gray-100 flex items-center justify-center shrink-0">
                                            <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&color=4f46e5&background=e0e7ff' }}" alt="Avatar" class="w-full h-full object-cover">
                                        </div>
                                        <span class="font-medium" style="color: var(--color-text-primary);">{{ $user->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-600 font-mono text-sm">{{ $user->username }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex w-36 justify-center items-center text-center px-3 py-1 text-xs font-semibold rounded-full border {{ $badgeClass }}">
                                        {{ $roleLabel }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center text-gray-500">{{ $user->created_at->format('d M Y') }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <button 
                                            type="button" 
                                            onclick="openEditModal({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ addslashes($user->username) }}', '{{ $user->role }}')"
                                            class="btn btn-secondary text-xs py-1.5 px-3">
                                            Edit
                                        </button>

                                        @if(auth()->id() !== $user->id)
                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus akun ini?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-ghost text-red-500 p-1.5 hover:bg-red-50 dark:hover:bg-red-500/10 dark:text-red-400 dark:hover:text-red-300 rounded-lg">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                        @else
                                        <span class="text-xs text-gray-400 italic px-2">(Anda)</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- Pagination -->
            @if($users->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- CREATE MODAL (HTML5 Dialog) -->
    <dialog id="createUserModal" class="p-0 bg-transparent rounded-2xl shadow-2xl backdrop:bg-black/60 backdrop:backdrop-blur-sm" style="margin: auto;">
        <div class="w-full bg-white rounded-2xl text-left overflow-hidden" style="background: var(--color-card-bg); width: 500px; max-width: 95vw;">
            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                <div class="px-6 py-5 border-b border-gray-100" style="background: rgba(0,0,0,0.02);">
                    <h3 class="text-lg font-bold" style="color: var(--color-text-primary);">Tambah Pengguna Baru</h3>
                </div>
                <div class="px-6 py-6 space-y-5">
                    <div>
                        <label class="form-label font-medium mb-1.5 block" style="color: var(--color-text-secondary);">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" name="name" required class="form-input w-full" placeholder="John Doe">
                    </div>
                    <div>
                        <label class="form-label font-medium mb-1.5 block" style="color: var(--color-text-secondary);">Username <span class="text-red-500">*</span></label>
                        <input type="text" name="username" required class="form-input w-full" placeholder="johndoe">
                    </div>
                    <div>
                        <label class="form-label font-medium mb-1.5 block" style="color: var(--color-text-secondary);">Password <span class="text-red-500">*</span></label>
                        <input type="password" name="password" required minlength="8" class="form-input w-full" placeholder="Minimal 8 karakter">
                    </div>
                    <div>
                        <label class="form-label font-medium mb-1.5 block" style="color: var(--color-text-secondary);">Role Hak Akses <span class="text-red-500">*</span></label>
                        <select name="role" required class="form-input w-full bg-white">
                            <option value="super_admin">Super Admin (Akses Penuh)</option>
                            <option value="it_team">IT Team</option>
                            <option value="finance_team">Finance Team</option>
                        </select>
                    </div>
                </div>
                <div class="px-6 py-4 flex justify-end gap-3 border-t border-gray-100" style="background: rgba(0,0,0,0.02);">
                    <button type="button" onclick="document.getElementById('createUserModal').close()" class="btn btn-secondary">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Pengguna</button>
                </div>
            </form>
        </div>
    </dialog>

    <!-- EDIT MODAL (HTML5 Dialog) -->
    <dialog id="editUserModal" class="p-0 bg-transparent rounded-2xl shadow-2xl backdrop:bg-black/60 backdrop:backdrop-blur-sm" style="margin: auto;">
        <div class="w-full bg-white rounded-2xl text-left overflow-hidden" style="background: var(--color-card-bg); width: 500px; max-width: 95vw;">
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="px-6 py-5 border-b border-gray-100" style="background: rgba(0,0,0,0.02);">
                    <h3 class="text-lg font-bold" style="color: var(--color-text-primary);">Edit Pengguna</h3>
                </div>
                <div class="px-6 py-6 space-y-5">
                    <div>
                        <label class="form-label font-medium mb-1.5 block" style="color: var(--color-text-secondary);">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" id="editName" name="name" required class="form-input w-full">
                    </div>
                    <div>
                        <label class="form-label font-medium mb-1.5 block" style="color: var(--color-text-secondary);">Username <span class="text-red-500">*</span></label>
                        <input type="text" id="editUsername" name="username" required class="form-input w-full">
                    </div>
                    <div>
                        <label class="form-label font-medium mb-1.5 block" style="color: var(--color-text-secondary);">Password Baru <span class="text-gray-400 text-xs font-normal">(Opsional)</span></label>
                        <input type="password" name="password" minlength="8" class="form-input w-full" placeholder="Kosongkan jika tidak ingin diubah">
                    </div>
                    <div>
                        <label class="form-label font-medium mb-1.5 block" style="color: var(--color-text-secondary);">Role Hak Akses <span class="text-red-500">*</span></label>
                        <select id="editRole" name="role" required class="form-input w-full bg-white">
                            <option value="super_admin">Super Admin (Akses Penuh)</option>
                            <option value="it_team">IT Team</option>
                            <option value="finance_team">Finance Team</option>
                        </select>
                    </div>
                </div>
                <div class="px-6 py-4 flex justify-end gap-3 border-t border-gray-100" style="background: rgba(0,0,0,0.02);">
                    <button type="button" onclick="document.getElementById('editUserModal').close()" class="btn btn-secondary">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </dialog>

    <!-- Modal Logic -->
    <script>
        function openEditModal(id, name, username, role) {
            const form = document.getElementById('editForm');
            form.action = `/users/${id}`;
            document.getElementById('editName').value = name;
            document.getElementById('editUsername').value = username;
            document.getElementById('editRole').value = role;
            document.getElementById('editUserModal').showModal();
        }
    </script>
</x-app-layout>
