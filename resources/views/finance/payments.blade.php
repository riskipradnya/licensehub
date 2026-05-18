<x-app-layout title="Process Payment" :breadcrumbs="[['label' => 'Finance', 'url' => '#'], ['label' => 'Process Payment']]">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold" style="color: var(--color-text-primary);">Process Payment</h2>
            <p class="text-sm mt-1" style="color: var(--color-text-secondary);">Daftar lisensi yang menunggu pembayaran (Expired / Expiring Soon)</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('payments.history') }}" class="btn btn-secondary text-sm">📋 Payment History</a>
        </div>
    </div>

    <div class="card p-0 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead><tr><th>License</th><th>Vendor</th><th>Expiry Date</th><th>Cost</th><th>Status</th><th class="text-center">Action</th></tr></thead>
                <tbody>
                    @forelse($licenses as $license)
                    <tr>
                        <td class="font-medium">
                            <a href="{{ route('licenses.show', $license) }}" class="hover:underline" style="color: var(--color-primary);">{{ $license->name }}</a>
                        </td>
                        <td>{{ $license->vendor->name ?? '—' }}</td>
                        <td>
                            @if($license->expiry_date)
                                <span class="{{ $license->days_until_expiry <= 0 ? 'text-red-500 font-semibold' : '' }}">
                                    {{ $license->expiry_date->format('d M Y') }}
                                    <span class="text-xs ml-1">
                                        ({{ $license->days_until_expiry <= 0 ? abs($license->days_until_expiry) . ' hari lalu' : 'H-' . $license->days_until_expiry }})
                                    </span>
                                </span>
                            @else
                                —
                            @endif
                        </td>
                        <td class="font-semibold">{{ $license->formatted_cost }}</td>
                        <td><x-status-badge :status="$license->status" size="sm" /></td>
                        <td class="text-center">
                            <a href="{{ route('payments.renew', $license->id) }}" class="btn btn-primary text-xs py-1 px-3">💳 Pay Now</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-8">
                            <p class="text-sm" style="color: var(--color-text-secondary);">Tidak ada lisensi yang memerlukan pembayaran saat ini.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($licenses->hasPages())
        <div class="px-6 py-3 border-t" style="border-color: var(--color-border);">
            {{ $licenses->links('pagination::tailwind') }}
        </div>
        @endif
    </div>

    {{-- Flash toast: ditampilkan setelah redirect()->with('success'/'error') --}}
    @if(session('success') || session('error'))
    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            setTimeout(() => {
                @if(session('success'))
                window.dispatchEvent(new CustomEvent('toast', {
                    detail: { message: @json(session('success')), type: 'success' }
                }));
                @endif
                @if(session('error'))
                window.dispatchEvent(new CustomEvent('toast', {
                    detail: { message: @json(session('error')), type: 'error' }
                }));
                @endif
            }, 300);
        });
    </script>
    @endpush
    @endif

</x-app-layout>
