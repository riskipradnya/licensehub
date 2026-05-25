<x-app-layout>
    <x-slot name="title">Profil Saya</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white" style="color: var(--color-text-primary);">Profil Pengguna</h1>
            <p class="text-sm mt-1 text-gray-500" style="color: var(--color-text-secondary);">Kelola identitas dan lihat riwayat aktivitas Anda.</p>
        </div>

        @if(session('success'))
            <div class="mb-4 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 shadow-sm flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- LEFT COLUMN (Identitas & Kredensial) -->
            <div class="lg:col-span-2 space-y-8">
                
                <!-- CARD 1: Identitas Diri (Editable) -->
                <div class="card p-0 shadow-lg rounded-2xl border border-gray-200 overflow-hidden" style="background: var(--color-card-bg);">
                    <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50" style="background: rgba(0,0,0,0.02);">
                        <h3 class="text-lg font-bold" style="color: var(--color-text-primary);">Identitas Diri</h3>
                        <p class="text-sm mt-1" style="color: var(--color-text-secondary);">Perbarui foto profil dan nama lengkap Anda.</p>
                    </div>
                    
                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="p-6">
                        @csrf
                        @method('PUT')
                        
                        <div class="flex flex-col sm:flex-row items-center sm:items-start gap-8 mb-8" x-data="{ photoName: null, photoPreview: null }">
                            
                            <!-- Avatar Upload Section -->
                            <div class="flex flex-col items-center">
                                <input type="file" name="avatar" class="hidden" x-ref="photo" accept="image/*"
                                    x-on:change="
                                        photoName = $refs.photo.files[0].name;
                                        const reader = new FileReader();
                                        reader.onload = (e) => {
                                            photoPreview = e.target.result;
                                        };
                                        reader.readAsDataURL($refs.photo.files[0]);
                                    ">

                                <div class="relative group">
                                    <div class="w-32 h-32 rounded-full overflow-hidden border-4 border-white shadow-xl bg-gray-100 flex items-center justify-center relative">
                                        <!-- Current / Fallback Avatar -->
                                        <img x-show="!photoPreview" src="{{ $user->avatar ? asset('storage/' . $user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&color=4f46e5&background=e0e7ff&size=200' }}" alt="Avatar" class="w-full h-full object-cover">
                                        
                                        <!-- New Image Preview -->
                                        <img x-show="photoPreview" :src="photoPreview" class="w-full h-full object-cover" style="display: none;">
                                    </div>
                                    <button type="button" x-on:click.prevent="$refs.photo.click()" class="absolute bottom-0 right-0 bg-indigo-600 hover:bg-indigo-700 text-white p-2.5 rounded-full shadow-lg transition-transform transform hover:scale-110">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    </button>
                                </div>
                                @error('avatar')
                                    <p class="mt-3 text-xs text-red-500 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Name Input Section -->
                            <div class="flex-1 w-full space-y-4">
                                <div>
                                    <label class="form-label font-medium mb-1.5 block" style="color: var(--color-text-secondary);">Nama Lengkap</label>
                                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="form-input w-full p-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 transition-all">
                                    @error('name')
                                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end pt-4 border-t border-gray-100">
                            <button type="submit" class="btn btn-primary px-6 py-2.5 rounded-xl font-semibold shadow-md hover:shadow-lg transition-all">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>

                <!-- CARD 2: Informasi Kredensial (Read-Only) -->
                <div class="card p-0 shadow-lg rounded-2xl border border-gray-200 overflow-hidden" style="background: var(--color-card-bg);">
                    <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50" style="background: rgba(0,0,0,0.02);">
                        <h3 class="text-lg font-bold" style="color: var(--color-text-primary);">Informasi Kredensial</h3>
                        <p class="text-sm mt-1 text-gray-500">Kredensial sistem bersifat aman dan diawasi.</p>
                    </div>
                    
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Username Login</p>
                                <p class="text-lg font-mono font-medium text-gray-800">{{ $user->username }}</p>
                            </div>
                            
                            <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Peran Akses (Role)</p>
                                @php
                                    $badgeClass = match($user->role) {
                                        'super_admin'     => 'bg-purple-100 text-purple-800 border-purple-200',
                                        'it_staff'        => 'bg-blue-100 text-blue-800 border-blue-200',
                                        'finance_manager' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                        'finance_staff'   => 'bg-green-100 text-green-800 border-green-200',
                                        default           => 'bg-gray-100 text-gray-800 border-gray-200',
                                    };
                                    $roleLabel = ucwords(str_replace('_', ' ', $user->role));
                                @endphp
                                <span class="inline-flex justify-center items-center px-4 py-1.5 text-xs font-bold rounded-full border {{ $badgeClass }} uppercase tracking-wide">
                                    {{ $roleLabel }}
                                </span>
                            </div>
                            
                            <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 md:col-span-2 flex items-center justify-between">
                                <div>
                                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Bergabung Sejak</p>
                                    <p class="text-sm font-medium text-gray-700">{{ $user->created_at->format('d M Y') }}</p>
                                </div>
                                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                        </div>

                        <div class="flex items-start gap-3 p-4 rounded-lg bg-amber-50 border border-amber-200 text-amber-800">
                            <svg class="w-5 h-5 shrink-0 mt-0.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <p class="text-sm leading-relaxed"><strong>Hubungi Super Admin</strong> jika Anda ingin mengubah username, mengatur ulang password, atau merubah status otoritas (Role) Anda di dalam sistem.</p>
                        </div>
                    </div>
                </div>

            </div>

            <!-- RIGHT COLUMN (Aktivitas Terakhir) -->
            <div class="space-y-6">
                
                <!-- CARD 3: Aktivitas Terakhir Anda -->
                <div class="card p-0 shadow-lg rounded-2xl border border-gray-200 overflow-hidden h-full" style="background: var(--color-card-bg);">
                    <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50" style="background: rgba(0,0,0,0.02);">
                        <h3 class="text-lg font-bold" style="color: var(--color-text-primary);">Aktivitas Terakhir Anda</h3>
                    </div>
                    
                    <div class="p-6">
                        @if($activities->isEmpty())
                            <div class="text-center py-8">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <p class="text-sm text-gray-500">Belum ada jejak aktivitas tercatat.</p>
                            </div>
                        @else
                            <div class="relative border-l-2 border-indigo-100 ml-3 space-y-8">
                                @foreach($activities as $activity)
                                    @php
                                        // Deteksi warna icon berdasarkan jenis event
                                        $iconColor = match($activity->event) {
                                            'created' => 'bg-emerald-100 text-emerald-600 border-emerald-200',
                                            'updated' => 'bg-amber-100 text-amber-600 border-amber-200',
                                            'deleted' => 'bg-red-100 text-red-600 border-red-200',
                                            'login'   => 'bg-blue-100 text-blue-600 border-blue-200',
                                            'logout'  => 'bg-gray-100 text-gray-600 border-gray-200',
                                            default   => 'bg-indigo-100 text-indigo-600 border-indigo-200',
                                        };

                                        // Deteksi icon (SVG)
                                        $iconSvg = match($activity->event) {
                                            'created' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>',
                                            'updated' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>',
                                            'deleted' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>',
                                            'login'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>',
                                            default   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>',
                                        };

                                        $subjectClass = $activity->subject_type ? class_basename($activity->subject_type) : null;
                                        
                                        // Human readable action
                                        $actionText = ucfirst($activity->event);
                                        if ($subjectClass) {
                                            $subjectName = $activity->subject->name ?? $activity->subject->invoice_number ?? $activity->subject->id ?? '';
                                            $actionText .= " {$subjectClass}";
                                            if ($subjectName) {
                                                $actionText .= " : <span class='font-semibold text-gray-800'>{$subjectName}</span>";
                                            }
                                        } else {
                                            $actionText = ucfirst($activity->description);
                                        }
                                    @endphp
                                    <div class="relative pl-6">
                                        <span class="absolute -left-[17px] top-1 flex h-8 w-8 items-center justify-center rounded-full border shadow-sm {{ $iconColor }}">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                {!! $iconSvg !!}
                                            </svg>
                                        </span>
                                        <div class="flex flex-col mb-1">
                                            <h3 class="text-sm font-medium" style="color: var(--color-text-primary);">
                                                {!! $actionText !!}
                                            </h3>
                                            <time class="text-xs text-gray-500 mt-1 flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                {{ $activity->created_at->diffForHumans() }}
                                            </time>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                        
                        <div class="mt-6 pt-4 border-t border-gray-100 text-center">
                            <a href="{{ route('audit-log.index') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800 transition-colors inline-flex items-center">
                                Lihat Semua Log Aktivitas
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
