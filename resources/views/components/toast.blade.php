{{-- Toast notification container - rendered once in app layout --}}
<div x-data="toast" class="fixed top-6 right-6 z-[9999] space-y-3 pointer-events-none"
     x-on:toast.window="show($event.detail.message, $event.detail.type || 'success', $event.detail.duration || 4000)">
    <template x-for="t in toasts" :key="t.id">
        <div class="toast pointer-events-auto"
             :class="{
                 'toast--success': t.type === 'success',
                 'toast--error': t.type === 'error',
                 'toast--warning': t.type === 'warning'
             }">
            {{-- ICON --}}
            <template x-if="t.type === 'success'">
                <svg class="w-5 h-5 shrink-0" style="color: var(--color-status-active);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </template>
            <template x-if="t.type === 'error'">
                <svg class="w-5 h-5 shrink-0" style="color: var(--color-status-danger);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </template>
            <template x-if="t.type === 'warning'">
                <svg class="w-5 h-5 shrink-0" style="color: var(--color-status-warning);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
            </template>

            {{-- MESSAGE --}}
            <p class="text-sm font-medium flex-1" style="color: var(--color-text-primary);" x-text="t.message"></p>

            {{-- CLOSE --}}
            <button @click="dismiss(t.id)" class="shrink-0 opacity-50 hover:opacity-100 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </template>
</div>
