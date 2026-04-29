<x-app-layout title="Notifications & Alerts" :breadcrumbs="[['label' => 'Monitoring', 'url' => '#'], ['label' => 'Notifications']]">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold" style="color: var(--color-text-primary);">Notifications & Alerts</h2>
            <p class="text-sm mt-1" style="color: var(--color-text-secondary);">Pantau peringatan lisensi yang akan kedaluwarsa</p>
        </div>
        <button class="btn btn-secondary text-sm" id="mark-all-read">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            Mark All as Read
        </button>
    </div>

    {{-- TABS --}}
    <div x-data="{ tab: 'all' }" class="mb-6">
        <div class="flex gap-1 p-1 rounded-xl w-fit" style="background: var(--color-card-bg); border: 1px solid var(--color-border);">
            <button @click="tab = 'all'" :class="tab === 'all' ? 'bg-indigo-500 text-white' : ''" class="px-4 py-2 rounded-lg text-sm font-medium transition" id="tab-all">All (12)</button>
            <button @click="tab = 'urgent'" :class="tab === 'urgent' ? 'bg-red-500 text-white' : ''" class="px-4 py-2 rounded-lg text-sm font-medium transition" id="tab-urgent">⚠ Urgent (3)</button>
            <button @click="tab = 'resolved'" :class="tab === 'resolved' ? 'bg-green-500 text-white' : ''" class="px-4 py-2 rounded-lg text-sm font-medium transition" id="tab-resolved">✅ Resolved (7)</button>
            <button @click="tab = 'sent'" :class="tab === 'sent' ? 'bg-blue-500 text-white' : ''" class="px-4 py-2 rounded-lg text-sm font-medium transition" id="tab-sent">📧 Sent (2)</button>
        </div>

        {{-- NOTIFICATION LIST --}}
        <div class="space-y-3 mt-4">
            @php
            $notifications = [
                ['title' => 'Kaspersky Endpoint Security', 'desc' => 'Kedaluwarsa besok! Segera lakukan perpanjangan.', 'level' => 'danger', 'label' => 'H-1 — KRITIS', 'time' => '14:23', 'tab' => 'urgent'],
                ['title' => 'Oracle Database Enterprise', 'desc' => 'Lisensi akan kedaluwarsa dalam 7 hari. Hubungi vendor untuk quotation.', 'level' => 'warning', 'label' => 'H-7 — PERINGATAN', 'time' => '08:00', 'tab' => 'urgent'],
                ['title' => 'Microsoft 365 Business', 'desc' => 'Lisensi akan kedaluwarsa dalam 14 hari. Pastikan anggaran tersedia.', 'level' => 'warning', 'label' => 'H-14 — PERINGATAN', 'time' => 'Yesterday', 'tab' => 'urgent'],
                ['title' => 'Windows Server 2022', 'desc' => 'Lisensi akan kedaluwarsa dalam 21 hari.', 'level' => 'info', 'label' => 'H-21 — INFO', 'time' => '2d ago', 'tab' => 'all'],
                ['title' => 'Adobe Creative Cloud', 'desc' => 'Lisensi berhasil diperpanjang hingga 10 Apr 2027.', 'level' => 'active', 'label' => 'RESOLVED', 'time' => '3d ago', 'tab' => 'resolved'],
                ['title' => 'ESET NOD32 Antivirus', 'desc' => 'Lisensi akan kedaluwarsa dalam 26 hari.', 'level' => 'info', 'label' => 'H-26 — INFO', 'time' => '3d ago', 'tab' => 'all'],
                ['title' => 'VMware vSphere', 'desc' => 'Perpanjangan lisensi berhasil diproses.', 'level' => 'active', 'label' => 'RESOLVED', 'time' => '1w ago', 'tab' => 'resolved'],
                ['title' => 'Email notifikasi terkirim ke Finance', 'desc' => 'Peringatan H-7 untuk Oracle Database dikirim ke finance@company.com', 'level' => 'info', 'label' => 'EMAIL SENT', 'time' => '08:01', 'tab' => 'sent'],
            ];
            @endphp

            @foreach($notifications as $notif)
            <div x-show="tab === 'all' || tab === '{{ $notif['tab'] }}'"
                 x-transition
                 class="card flex items-start gap-4 hover:shadow-md transition-shadow"
                 style="border-left: 4px solid var(--color-status-{{ $notif['level'] }});">
                {{-- Icon --}}
                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
                     style="background: var(--color-status-{{ $notif['level'] }}-bg);">
                    @if($notif['level'] === 'danger')
                        <svg class="w-5 h-5" style="color: var(--color-status-danger);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                    @elseif($notif['level'] === 'warning')
                        <svg class="w-5 h-5" style="color: var(--color-status-warning);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    @elseif($notif['level'] === 'active')
                        <svg class="w-5 h-5" style="color: var(--color-status-active);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    @else
                        <svg class="w-5 h-5" style="color: var(--color-status-info);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    @endif
                </div>

                {{-- Content --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <h4 class="text-sm font-semibold" style="color: var(--color-text-primary);">{{ $notif['title'] }}</h4>
                                <x-status-badge :status="$notif['level']" :label="$notif['label']" size="sm" />
                            </div>
                            <p class="text-sm mt-1" style="color: var(--color-text-secondary);">{{ $notif['desc'] }}</p>
                        </div>
                        <span class="text-xs shrink-0" style="color: var(--color-text-secondary);">{{ $notif['time'] }}</span>
                    </div>
                    @if($notif['level'] === 'danger' || $notif['level'] === 'warning')
                    <div class="flex gap-2 mt-3">
                        <a href="/licenses/1" class="btn btn-secondary text-xs py-1.5 px-3">View Detail</a>
                        <a href="/payments/process/1" class="btn btn-primary text-xs py-1.5 px-3">Process Payment →</a>
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>

</x-app-layout>
