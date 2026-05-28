<x-app-layout title="Notifications & Alerts" :breadcrumbs="[['label' => 'Monitoring', 'url' => '#'], ['label' => 'Notifications']]">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold" style="color: var(--color-text-primary);">Notifications & Alerts</h2>
            <p class="text-sm mt-1" style="color: var(--color-text-secondary);">Pantau peringatan lisensi yang akan kedaluwarsa</p>
        </div>
        <form action="{{ url('/notifications/mark-all-read') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-secondary text-sm" id="mark-all-read">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Mark All as Read
            </button>
        </form>
    </div>

    {{-- TABS --}}
    <div x-data="{ tab: '{{ request('tab', 'all') }}' }" class="mb-6">
        <div class="flex gap-1 p-1 rounded-xl w-fit" style="background: var(--color-card-bg); border: 1px solid var(--color-border); overflow-x: auto; max-width: 100%;">
            <button @click="window.location.href = '?tab=all'" :class="tab === 'all' ? 'bg-indigo-500 text-white' : ''" class="px-4 py-2 rounded-lg text-sm font-medium transition whitespace-nowrap" id="tab-all">All ({{ $counts['all'] }})</button>
            <button @click="window.location.href = '?tab=expired'" :class="tab === 'expired' ? 'bg-red-700 text-white' : ''" class="px-4 py-2 rounded-lg text-sm font-medium transition whitespace-nowrap" id="tab-expired">🔴 Expired ({{ $counts['expired'] }})</button>
            <button @click="window.location.href = '?tab=urgent'" :class="tab === 'urgent' ? 'bg-red-500 text-white' : ''" class="px-4 py-2 rounded-lg text-sm font-medium transition whitespace-nowrap" id="tab-urgent">⚠ Urgent ({{ $counts['urgent'] }})</button>
            <button @click="window.location.href = '?tab=warning'" :class="tab === 'warning' ? 'bg-orange-500 text-white' : ''" class="px-4 py-2 rounded-lg text-sm font-medium transition whitespace-nowrap" id="tab-warning">⚠️ Warning ({{ $counts['warning'] }})</button>
            <button @click="window.location.href = '?tab=reminder'" :class="tab === 'reminder' ? 'bg-amber-400 text-white' : ''" class="px-4 py-2 rounded-lg text-sm font-medium transition whitespace-nowrap" id="tab-reminder">🔔 Reminder ({{ $counts['reminder'] }})</button>
            <button @click="window.location.href = '?tab=resolved'" :class="tab === 'resolved' ? 'bg-green-500 text-white' : ''" class="px-4 py-2 rounded-lg text-sm font-medium transition whitespace-nowrap" id="tab-resolved">✅ Resolved ({{ $counts['resolved'] }})</button>
            <button @click="window.location.href = '?tab=sent'" :class="tab === 'sent' ? 'bg-blue-500 text-white' : ''" class="px-4 py-2 rounded-lg text-sm font-medium transition whitespace-nowrap" id="tab-sent">📧 Sent ({{ $counts['sent'] }})</button>
        </div>

        {{-- NOTIFICATION LIST --}}
        <div class="space-y-3 mt-4">
            @forelse($notifications as $notification)
            <div x-transition
                 class="card flex items-start gap-4 hover:shadow-md transition-shadow {{ $notification->read_at ? 'opacity-60 bg-gray-50' : 'bg-white' }}"
                 style="border-left: 4px solid {{ ($notification->data['level'] ?? 'info') === 'critical' ? '#b91c1c' : 'var(--color-status-' . ($notification->data['level'] ?? 'info') . ')' }};">
                {{-- Icon --}}
                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
                     style="background: {{ ($notification->data['level'] ?? 'info') === 'critical' ? '#fef2f2' : 'var(--color-status-' . ($notification->data['level'] ?? 'info') . '-bg)' }};">
                    @if(($notification->data['level'] ?? 'info') === 'critical')
                        <svg class="w-5 h-5" style="color: #b91c1c;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    @elseif(($notification->data['level'] ?? 'info') === 'danger')
                        <svg class="w-5 h-5" style="color: var(--color-status-danger);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                    @elseif(($notification->data['level'] ?? 'info') === 'warning')
                        <svg class="w-5 h-5" style="color: var(--color-status-warning);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    @elseif(($notification->data['level'] ?? 'info') === 'active')
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
                                <h4 class="text-sm font-semibold" style="color: var(--color-text-primary);">{{ $notification->data['title'] ?? 'Notification' }}</h4>
                                @if(isset($notification->data['level']) && !empty($notification->data['label']))
                                <x-status-badge :status="$notification->data['level']" :label="$notification->data['label'] ?? ''" size="sm" />
                                @endif
                            </div>
                            <p class="text-sm mt-1" style="color: var(--color-text-secondary);">{{ $notification->data['desc'] ?? '' }}</p>
                        </div>
                        <span class="text-xs shrink-0" style="color: var(--color-text-secondary);">{{ $notification->created_at->diffForHumans() }}</span>
                    </div>
                    @if(in_array($notification->data['level'], ['critical', 'danger', 'warning', 'info']))
                    <div class="flex gap-2 mt-3">
                        <a href="{{ url('/licenses/' . $notification->data['license_id']) }}" class="btn btn-secondary text-xs py-1.5 px-3">View Detail</a>
                        <a href="{{ route('payments.renew', $notification->data['license_id']) }}" class="btn btn-primary text-xs py-1.5 px-3">Process Payment →</a>
                    </div>
                    @endif
                </div>
            </div>
            @empty
            <div class="text-center py-10">
                <svg class="w-12 h-12 mx-auto mb-3" style="color: var(--color-text-secondary); opacity: 0.5;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                <p class="text-sm font-medium" style="color: var(--color-text-secondary);">Belum ada notifikasi.</p>
            </div>
            @endforelse
        </div>

        <div class="mt-6 pt-4 border-t border-gray-100 dark:border-gray-800/50">
            {{ $notifications->appends(['tab' => request('tab', 'all')])->links() }}
        </div>
    </div>

</x-app-layout>
