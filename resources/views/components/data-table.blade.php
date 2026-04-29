@props([
    'headers' => [],
    'rows' => [],
    'emptyMessage' => 'Tidak ada data ditemukan.',
    'sortable' => false,
])

<div class="card p-0 overflow-hidden" x-data="{ sortBy: '', sortDir: 'asc' }">
    <div class="overflow-x-auto">
        <table class="data-table" id="{{ $attributes->get('id', 'data-table') }}">
            <thead>
                <tr>
                    @foreach($headers as $key => $header)
                        <th @if($sortable) @click="sortBy = '{{ $key }}'; sortDir = sortDir === 'asc' ? 'desc' : 'asc'" class="cursor-pointer select-none hover:text-indigo-600 transition" @endif>
                            <div class="flex items-center gap-1">
                                {{ $header }}
                                @if($sortable)
                                <svg class="w-3 h-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                                </svg>
                                @endif
                            </div>
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $row)
                    <tr>
                        @foreach($headers as $key => $header)
                            <td>{!! $row[$key] ?? '-' !!}</td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($headers) }}" class="text-center py-12">
                            <x-empty-state :message="$emptyMessage" />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- SLOT FOR PAGINATION --}}
    @if(isset($pagination))
    <div class="px-4 py-3 border-t" style="border-color: var(--color-border);">
        {{ $pagination }}
    </div>
    @endif
</div>
