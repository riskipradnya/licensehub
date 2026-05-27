<x-app-layout>
    <x-slot name="title">Audit Log System</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6 text-slate-200">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-slate-200 dark:border-slate-700/50 pb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white flex items-center gap-3">
                    <svg class="w-7 h-7 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    Audit Log System
                </h1>
                <p class="text-sm mt-1 text-slate-400">Pantau seluruh rekam jejak aktivitas, perubahan data, dan intervensi sistem.</p>
            </div>
            <div class="flex gap-2">
                <span class="px-3 py-1 bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 rounded-full text-xs font-semibold tracking-wide shadow-sm">
                    Live Tracking Active
                </span>
            </div>
        </div>

        <!-- Vertical Timeline Container -->
        <div class="bg-white dark:bg-[#1E293B] border border-slate-200 dark:border-slate-700/50 shadow-lg rounded-xl p-6 lg:p-8">
            <div class="relative border-l border-slate-200 dark:border-slate-700/50 ml-4 space-y-10">
                
                @forelse($activities as $activity)
                    @php
                        // 1. Safe Model Name Extraction
                        $subjectClass = $activity->subject_type ? class_basename($activity->subject_type) : null;
                        
                        // 2. Dynamic Action Text formatting
                        $actionText = ucfirst($activity->event);
                        if ($subjectClass) {
                            $subjectName = $activity->subject->name ?? $activity->subject->invoice_number ?? $activity->subject->id ?? '';
                            
                            $actionText = match($activity->event) {
                                'created' => "Menambahkan <b>{$subjectClass}</b> baru",
                                'updated' => "Memperbarui data <b>{$subjectClass}</b>",
                                'deleted' => "Menghapus <b>{$subjectClass}</b>",
                                default   => ucfirst($activity->event) . " <b>{$subjectClass}</b>",
                            };

                            if ($subjectName) {
                                $actionText .= " #<span class='text-slate-900 dark:text-white font-semibold'>{$subjectName}</span>";
                                
                                if ($activity->subject_type === 'App\Models\Payment') {
                                    $licenseName = $activity->subject?->license?->name;
                                    $actionText .= $licenseName ? " <span class='text-indigo-300'>({$licenseName})</span>" : "";
                                }
                            }
                        } else {
                            $actionText = ucfirst($activity->description);
                        }

                        // 3. Optional: badge colors/icons (not strictly used if avatar is there, but kept to prevent crashes if referenced)
                        $iconColor = match($activity->event) {
                            'created' => 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30',
                            'updated' => 'bg-amber-500/20 text-amber-400 border-amber-500/30',
                            'deleted' => 'bg-red-500/20 text-red-400 border-red-500/30',
                            'login'   => 'bg-blue-500/20 text-blue-400 border-blue-500/30',
                            'logout'  => 'bg-slate-500/20 text-slate-400 border-slate-500/30',
                            default   => 'bg-indigo-500/20 text-indigo-400 border-indigo-500/30',
                        };
                    @endphp

                    <div class="relative pl-8">
                        <!-- Icon Node (Avatar) -->
                        <span class="absolute -left-4 top-1 flex h-8 w-8 items-center justify-center rounded-full border border-slate-200 dark:border-slate-700/50 bg-white dark:bg-[#1E293B] shadow-sm overflow-hidden shrink-0">
                            @if($activity->causer)
                                <img src="{{ $activity->causer->avatar ? asset('storage/' . $activity->causer->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($activity->causer->name ?? 'A') . '&color=4f46e5&background=e0e7ff' }}" alt="Avatar" class="w-full h-full object-cover">
                            @else
                                <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            @endif
                        </span>

                        <div class="flex flex-col sm:flex-row sm:items-baseline sm:justify-between mb-1">
                            <h3 class="text-sm font-medium text-slate-800 dark:text-slate-300">
                                {!! $actionText !!}
                            </h3>
                            <time class="text-xs text-slate-500 sm:ml-2">{{ $activity->created_at->diffForHumans() }} &bull; {{ $activity->created_at->format('d M Y H:i') }}</time>
                        </div>
                        
                        <div class="text-sm text-slate-400 mb-3 flex items-center gap-2">
                            <div class="h-5 w-5 rounded-full bg-slate-700 flex items-center justify-center text-[10px] font-bold text-white shrink-0">
                                {{ substr($activity->causer->name ?? 'S', 0, 1) }}
                            </div>
                            <span class="font-medium text-indigo-300">{{ $activity->causer->name ?? 'System / Webhook' }}</span>
                        </div>

                        <!-- Diff Style Detail for Updates -->
                        @if($activity->event === 'updated' && isset($activity->properties['old']) && isset($activity->properties['attributes']))
                            <div class="mt-3 bg-slate-50 dark:bg-[#0F172A] border border-slate-200 dark:border-slate-700/50 rounded-lg overflow-hidden">
                                <div class="px-4 py-2 border-b border-slate-200 dark:border-slate-700/50 bg-slate-100 dark:bg-slate-800/30 text-xs font-semibold text-slate-700 dark:text-slate-400">
                                    Detail Perubahan Data
                                </div>
                                <div class="p-4 grid gap-3">
                                    @foreach($activity->properties['attributes'] as $key => $newValue)
                                        @php
                                            $oldValue = $activity->properties['old'][$key] ?? null;
                                        @endphp
                                        @if($oldValue !== $newValue)
                                            <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-6 text-sm">
                                                <div class="font-medium text-slate-500 w-32 shrink-0">{{ ucfirst(str_replace('_', ' ', $key)) }}</div>
                                                <div class="flex-1 flex flex-col sm:flex-row items-start sm:items-center gap-2 sm:gap-4">
                                                    <!-- Old Value -->
                                                    <div class="flex-1 bg-red-500/10 border border-red-500/20 text-red-600 dark:text-red-300 px-3 py-1.5 rounded-md break-all">
                                                        <del class="opacity-80">
                                                            @if($key === 'expiry_date' && $oldValue && is_string($oldValue))
                                                                {{ \Carbon\Carbon::parse($oldValue)->format('d M Y') }}
                                                            @else
                                                                {{ is_array($oldValue) ? json_encode($oldValue) : ($oldValue ?: 'null') }}
                                                            @endif
                                                        </del>
                                                    </div>
                                                    
                                                    <!-- Arrow -->
                                                    <div class="text-slate-500 shrink-0 hidden sm:block">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                                    </div>
                                                    <div class="text-slate-500 shrink-0 sm:hidden flex justify-center w-full">
                                                        <svg class="w-4 h-4 rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                                    </div>

                                                    <!-- New Value -->
                                                    <div class="flex-1 bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-300 px-3 py-1.5 rounded-md break-all font-medium">
                                                        @if($key === 'expiry_date' && $newValue && is_string($newValue))
                                                            {{ \Carbon\Carbon::parse($newValue)->format('d M Y') }}
                                                        @else
                                                            {{ is_array($newValue) ? json_encode($newValue) : ($newValue ?: 'null') }}
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @elseif(!empty($activity->properties) && count($activity->properties) > 0)
                            <!-- General Payload Viewer -->
                            <div class="mt-3 bg-slate-50 dark:bg-[#0F172A] border border-slate-200 dark:border-slate-700/50 rounded-lg p-4">
                                <pre class="text-xs text-slate-800 dark:text-slate-400 overflow-x-auto"><code>{{ json_encode($activity->properties, JSON_PRETTY_PRINT) }}</code></pre>
                            </div>
                        @endif

                    </div>
                @empty
                    <div class="relative pl-8 pb-4">
                        <span class="absolute -left-4 top-1 flex h-8 w-8 items-center justify-center rounded-full border border-slate-200 dark:border-slate-700/50 bg-white dark:bg-[#1E293B] text-slate-500">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </span>
                        <p class="text-slate-400">Belum ada rekam aktivitas di dalam sistem.</p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination Links -->
            @if($activities->hasPages())
                <div class="mt-10 pt-6 border-t border-slate-700/50">
                    {{ $activities->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
