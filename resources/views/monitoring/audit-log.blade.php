<x-app-layout title="Audit Log" :breadcrumbs="[['label' => 'Monitoring', 'url' => '#'], ['label' => 'Audit Log']]">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold" style="color: var(--color-text-primary);">Audit Log</h2>
            <p class="text-sm mt-1" style="color: var(--color-text-secondary);">Riwayat perubahan data di seluruh sistem</p>
        </div>
        <button class="btn btn-secondary text-sm" id="export-audit">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            Export Log
        </button>
    </div>

    {{-- FILTERS --}}
    <div class="card mb-6">
        <div class="flex flex-col md:flex-row gap-3">
            <input type="date" class="form-input w-full md:w-auto" id="audit-date-from">
            <span class="hidden md:flex items-center text-sm" style="color: var(--color-text-secondary);">to</span>
            <input type="date" class="form-input w-full md:w-auto" id="audit-date-to">
            <select class="form-input w-full md:w-40"><option value="">All Users</option><option>Admin</option><option>IT Staff</option><option>Finance Mgr</option></select>
            <select class="form-input w-full md:w-40"><option value="">All Actions</option><option>Created</option><option>Updated</option><option>Deleted</option><option>Login</option></select>
        </div>
    </div>

    {{-- TIMELINE --}}
    <div class="space-y-0">
        @php
        $logs = [
            ['time' => '14:23', 'date' => 'Today', 'user' => 'Admin', 'avatar' => 'A', 'action' => 'Updated', 'target' => 'Kaspersky Endpoint Security', 'detail' => 'Changed status from "Active" to "Expired"', 'color' => 'warning'],
            ['time' => '11:05', 'date' => 'Today', 'user' => 'IT Staff', 'avatar' => 'I', 'action' => 'Uploaded', 'target' => 'Oracle Database Enterprise', 'detail' => 'Added document: Quotation_Oracle_2026.pdf (3.2 MB)', 'color' => 'info'],
            ['time' => '09:30', 'date' => 'Today', 'user' => 'Finance Mgr', 'avatar' => 'F', 'action' => 'Processed', 'target' => 'Adobe Creative Cloud', 'detail' => 'Payment of Rp 18.000.000 via Bank Transfer — Status: Paid', 'color' => 'active'],
            ['time' => '16:42', 'date' => 'Yesterday', 'user' => 'Admin', 'avatar' => 'A', 'action' => 'Created', 'target' => 'Slack Enterprise', 'detail' => 'New license added: Subscription, 100 seats, Rp 9.500.000', 'color' => 'primary'],
            ['time' => '14:10', 'date' => 'Yesterday', 'user' => 'IT Staff', 'avatar' => 'I', 'action' => 'Updated', 'target' => 'Microsoft 365 Business', 'detail' => 'Changed seat count from 45 to 50', 'color' => 'warning'],
            ['time' => '10:00', 'date' => 'Yesterday', 'user' => 'Admin', 'avatar' => 'A', 'action' => 'Deleted', 'target' => 'Legacy Antivirus Pro', 'detail' => 'License removed from system', 'color' => 'danger'],
            ['time' => '08:15', 'date' => '2 days ago', 'user' => 'System', 'avatar' => 'S', 'action' => 'Alert Sent', 'target' => 'Oracle Database Enterprise', 'detail' => 'H-7 notification sent to IT and Finance departments', 'color' => 'info'],
            ['time' => '17:30', 'date' => '3 days ago', 'user' => 'Finance Mgr', 'avatar' => 'F', 'action' => 'Generated', 'target' => 'Invoice INV-2026-042', 'detail' => 'Internal invoice created for Adobe CC renewal', 'color' => 'active'],
        ];
        @endphp

        @foreach($logs as $i => $log)
        <div class="flex gap-4">
            {{-- Timeline Line --}}
            <div class="flex flex-col items-center">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold shrink-0 z-10"
                     style="background: var(--color-status-{{ $log['color'] }}, var(--color-primary));">
                    {{ $log['avatar'] }}
                </div>
                @if(!$loop->last)
                <div class="w-0.5 flex-1 min-h-[24px]" style="background: var(--color-border);"></div>
                @endif
            </div>

            {{-- Content --}}
            <div class="card flex-1 mb-3">
                <div class="flex items-start justify-between gap-2">
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-sm font-semibold" style="color: var(--color-text-primary);">{{ $log['user'] }}</span>
                            <span class="text-xs px-2 py-0.5 rounded-md font-medium"
                                  style="background: var(--color-status-{{ $log['color'] }}-bg); color: var(--color-status-{{ $log['color'] }});">
                                {{ $log['action'] }}
                            </span>
                            <span class="text-sm font-medium" style="color: var(--color-text-primary);">{{ $log['target'] }}</span>
                        </div>
                        <p class="text-sm mt-1.5" style="color: var(--color-text-secondary);">{{ $log['detail'] }}</p>
                    </div>
                    <div class="text-right shrink-0">
                        <p class="text-xs font-medium" style="color: var(--color-text-secondary);">{{ $log['time'] }}</p>
                        <p class="text-[10px]" style="color: var(--color-text-secondary);">{{ $log['date'] }}</p>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- LOAD MORE --}}
    <div class="text-center mt-6">
        <button class="btn btn-secondary" id="load-more-logs">Load More</button>
    </div>

</x-app-layout>
