<x-app-layout title="User & Role Management" :breadcrumbs="[['label' => 'Settings', 'url' => '#'], ['label' => 'User Management']]">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold" style="color: var(--color-text-primary);">User & Role Management</h2>
            <p class="text-sm mt-1" style="color: var(--color-text-secondary);">Kelola akun pengguna dan hak akses</p>
        </div>
        <button class="btn btn-primary" @click="$dispatch('open-modal-add-user')" id="add-user-btn">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
            Add User
        </button>
    </div>

    {{-- ROLE CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-6">
        @php
        $roles = [
            ['name' => 'Super Admin', 'count' => 2, 'color' => '#6366f1'],
            ['name' => 'IT Staff', 'count' => 5, 'color' => '#22c55e'],
            ['name' => 'Finance Manager', 'count' => 2, 'color' => '#f59e0b'],
            ['name' => 'Finance Staff', 'count' => 3, 'color' => '#8b5cf6'],
        ];
        @endphp
        @foreach($roles as $r)
        <div class="card text-center" style="border-top: 3px solid {{ $r['color'] }};">
            <p class="text-2xl font-bold" style="color: {{ $r['color'] }};">{{ $r['count'] }}</p>
            <p class="text-xs uppercase tracking-wider mt-1" style="color: var(--color-text-secondary);">{{ $r['name'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- USERS TABLE --}}
    <div class="card p-0 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table" id="users-table">
                <thead><tr><th>User</th><th>Email</th><th>Role</th><th>Department</th><th>Last Login</th><th>Status</th><th class="text-center">Actions</th></tr></thead>
                <tbody>
                    @php
                    $users = [
                        ['name' => 'Ahmad Rizky', 'email' => 'admin@company.com', 'role' => 'Super Admin', 'dept' => 'IT', 'login' => '14 Apr 2026, 14:23', 'active' => true, 'avatar' => 'AR', 'color' => '#6366f1'],
                        ['name' => 'Budi Santoso', 'email' => 'budi@company.com', 'role' => 'IT Staff', 'dept' => 'IT', 'login' => '14 Apr 2026, 09:15', 'active' => true, 'avatar' => 'BS', 'color' => '#22c55e'],
                        ['name' => 'Citra Dewi', 'email' => 'citra@company.com', 'role' => 'Finance Manager', 'dept' => 'Finance', 'login' => '13 Apr 2026, 17:00', 'active' => true, 'avatar' => 'CD', 'color' => '#f59e0b'],
                        ['name' => 'Dani Pratama', 'email' => 'dani@company.com', 'role' => 'IT Staff', 'dept' => 'IT', 'login' => '12 Apr 2026, 11:30', 'active' => true, 'avatar' => 'DP', 'color' => '#22c55e'],
                        ['name' => 'Eka Putri', 'email' => 'eka@company.com', 'role' => 'Finance Staff', 'dept' => 'Finance', 'login' => '10 Apr 2026, 08:45', 'active' => false, 'avatar' => 'EP', 'color' => '#8b5cf6'],
                    ];
                    @endphp
                    @foreach($users as $u)
                    <tr>
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold shrink-0" style="background: {{ $u['color'] }};">{{ $u['avatar'] }}</div>
                                <span class="font-medium">{{ $u['name'] }}</span>
                            </div>
                        </td>
                        <td class="text-sm">{{ $u['email'] }}</td>
                        <td><span class="text-xs px-2 py-0.5 rounded-md font-medium" style="background: {{ $u['color'] }}20; color: {{ $u['color'] }};">{{ $u['role'] }}</span></td>
                        <td>{{ $u['dept'] }}</td>
                        <td class="text-xs">{{ $u['login'] }}</td>
                        <td><x-status-badge :status="$u['active'] ? 'active' : 'inactive'" size="sm" /></td>
                        <td class="text-center">
                            <div class="flex items-center justify-center gap-1">
                                <button class="btn-ghost p-1.5 rounded-lg" title="Edit"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>
                                <button class="btn-ghost p-1.5 rounded-lg hover:text-red-500" title="Deactivate"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg></button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- ADD USER MODAL --}}
    <x-modal id="add-user" title="Add New User" maxWidth="md">
        <form>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div class="md:col-span-2"><label class="form-label">Full Name *</label><input type="text" class="form-input" placeholder="Nama lengkap"></div>
                <div><label class="form-label">Email *</label><input type="email" class="form-input" placeholder="email@company.com"></div>
                <div><label class="form-label">Role *</label><select class="form-input"><option>Super Admin</option><option>IT Staff</option><option>Finance Manager</option><option>Finance Staff</option></select></div>
                <div><label class="form-label">Password *</label><input type="password" class="form-input" placeholder="Minimal 8 karakter"></div>
                <div><label class="form-label">Confirm Password *</label><input type="password" class="form-input" placeholder="Ulangi password"></div>
            </div>
            <x-slot:footer>
                <button type="button" @click="hide()" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary">Create User</button>
            </x-slot:footer>
        </form>
    </x-modal>

</x-app-layout>
