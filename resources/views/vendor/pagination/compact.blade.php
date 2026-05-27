@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex flex-col sm:flex-row justify-between items-center w-full gap-3 sm:gap-0">
        <div class="flex justify-between w-full sm:hidden gap-2">
            @if ($paginator->onFirstPage())
                <span class="relative inline-flex flex-1 justify-center items-center px-3 py-1 text-xs font-medium text-gray-400 bg-gray-50 dark:bg-[#1E293B] dark:text-gray-500 border border-gray-200 dark:border-gray-700 cursor-default rounded-md">
                    &laquo; Prev
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="relative inline-flex flex-1 justify-center items-center px-3 py-1 text-xs font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-[#1E293B] border border-gray-200 dark:border-gray-700 rounded-md hover:bg-gray-50 dark:hover:bg-gray-800 focus:outline-none transition ease-in-out duration-150">
                    &laquo; Prev
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="relative inline-flex flex-1 justify-center items-center px-3 py-1 text-xs font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-[#1E293B] border border-gray-200 dark:border-gray-700 rounded-md hover:bg-gray-50 dark:hover:bg-gray-800 focus:outline-none transition ease-in-out duration-150">
                    Next &raquo;
                </a>
            @else
                <span class="relative inline-flex flex-1 justify-center items-center px-3 py-1 text-xs font-medium text-gray-400 bg-gray-50 dark:bg-[#1E293B] dark:text-gray-500 border border-gray-200 dark:border-gray-700 cursor-default rounded-md">
                    Next &raquo;
                </span>
            @endif
        </div>

        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-[11px] text-gray-500 dark:text-gray-400 leading-5">
                    Showing <span class="font-medium">{{ $paginator->firstItem() }}</span>
                    - <span class="font-medium">{{ $paginator->lastItem() }}</span>
                    of <span class="font-medium">{{ $paginator->total() }}</span>
                </p>
            </div>

            <div>
                <span class="relative z-0 inline-flex shadow-sm rounded-md gap-1">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
                            <span class="relative inline-flex items-center px-2 py-1 text-[11px] font-medium text-gray-400 bg-gray-50 dark:bg-[#1E293B] dark:text-gray-500 border border-gray-200 dark:border-gray-700 cursor-default rounded-md" aria-hidden="true">
                                &lsaquo;
                            </span>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="relative inline-flex items-center px-2 py-1 text-[11px] font-medium text-gray-500 dark:text-gray-300 bg-white dark:bg-[#1E293B] border border-gray-200 dark:border-gray-700 rounded-md hover:bg-gray-50 dark:hover:bg-gray-800 focus:z-10 focus:outline-none transition ease-in-out duration-150" aria-label="{{ __('pagination.previous') }}">
                            &lsaquo;
                        </a>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <span aria-disabled="true">
                                <span class="relative inline-flex items-center px-2 py-1 text-[11px] font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-[#1E293B] border border-gray-200 dark:border-gray-700 cursor-default rounded-md">{{ $element }}</span>
                            </span>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page">
                                        <span class="relative inline-flex items-center px-2.5 py-1 text-[11px] font-bold text-white bg-indigo-600 border border-indigo-600 cursor-default rounded-md shadow-sm">{{ $page }}</span>
                                    </span>
                                @else
                                    <a href="{{ $url }}" class="relative inline-flex items-center px-2.5 py-1 text-[11px] font-medium text-gray-500 dark:text-gray-300 bg-white dark:bg-[#1E293B] border border-gray-200 dark:border-gray-700 rounded-md hover:bg-gray-50 dark:hover:bg-gray-800 focus:z-10 focus:outline-none transition ease-in-out duration-150">{{ $page }}</a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="relative inline-flex items-center px-2 py-1 text-[11px] font-medium text-gray-500 dark:text-gray-300 bg-white dark:bg-[#1E293B] border border-gray-200 dark:border-gray-700 rounded-md hover:bg-gray-50 dark:hover:bg-gray-800 focus:z-10 focus:outline-none transition ease-in-out duration-150" aria-label="{{ __('pagination.next') }}">
                            &rsaquo;
                        </a>
                    @else
                        <span aria-disabled="true" aria-label="{{ __('pagination.next') }}">
                            <span class="relative inline-flex items-center px-2 py-1 text-[11px] font-medium text-gray-400 bg-gray-50 dark:bg-[#1E293B] dark:text-gray-500 border border-gray-200 dark:border-gray-700 cursor-default rounded-md" aria-hidden="true">
                                &rsaquo;
                            </span>
                        </span>
                    @endif
                </span>
            </div>
        </div>
    </nav>
@endif
