@props([
    'paginator',
    'maxLinks' => 3, // Number of page links to show (excluding first/last)
])

@php
    // Calculate pagination data
    $currentPage = $paginator->currentPage();
    $lastPage = $paginator->lastPage();
    $total = $paginator->total();
    $perPage = $paginator->perPage();
    
    // Calculate showing range
    $from = ($currentPage - 1) * $perPage + 1;
    $to = min($currentPage * $perPage, $total);
    
    // Calculate page window
    $window = $maxLinks;
    $start = max($currentPage - floor($window / 2), 1);
    $end = min($start + $window - 1, $lastPage);
    
    // Adjust start if we're near the end
    if ($end - $start + 1 < $window) {
        $start = max($end - $window + 1, 1);
    }
    
    $pages = range($start, $end);
@endphp

@if ($paginator->hasPages())
    <div class="p-6 border-t border-gray-200 flex flex-col md:flex-row md:items-center justify-between gap-4">
        {{-- Showing text --}}
        <div class="text-sm text-gray-600">
            Showing 
            <span class="font-medium">{{ $from }}</span> 
            to 
            <span class="font-medium">{{ $to }}</span> 
            of 
            <span class="font-medium">{{ $total }}</span> 
            {{-- Dynamic label (you can pass this as prop) --}}
            @isset($label)
                {{ $label }}
            @else
                {{ Str::plural('item', $total) }}
            @endisset
        </div>

        {{-- Pagination buttons --}}
        <div class="flex items-center space-x-2">
            {{-- Previous button --}}
            <a 
                href="{{ $paginator->onFirstPage() ? '#' : $paginator->previousPageUrl() }}"
                @class([
                    'px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium transition-colors duration-200',
                    'hover:bg-gray-50 hover:border-gray-400 cursor-pointer' => !$paginator->onFirstPage(),
                    'bg-gray-100 text-gray-400 cursor-not-allowed' => $paginator->onFirstPage()
                ])
                @if($paginator->onFirstPage()) disabled @endif
            >
                Previous
            </a>

            {{-- Page 1 (always show if not in current window) --}}
            @if($start > 1)
                <a 
                    href="{{ $paginator->url(1) }}"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors duration-200 hover:border-gray-400"
                >
                    1
                </a>
                @if($start > 2)
                    <span class="px-2 text-gray-400">...</span>
                @endif
            @endif

            {{-- Page numbers --}}
            @foreach($pages as $page)
                <a 
                    href="{{ $paginator->url($page) }}"
                    @class([
                        'px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200',
                        'bg-indigo-600 text-white hover:bg-indigo-700' => $page == $currentPage,
                        'border border-gray-300 hover:bg-gray-50 hover:border-gray-400' => $page != $currentPage
                    ])
                >
                    {{ $page }}
                </a>
            @endforeach

            {{-- Last page (always show if not in current window) --}}
            @if($end < $lastPage)
                @if($end < $lastPage - 1)
                    <span class="px-2 text-gray-400">...</span>
                @endif
                <a 
                    href="{{ $paginator->url($lastPage) }}"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors duration-200 hover:border-gray-400"
                >
                    {{ $lastPage }}
                </a>
            @endif

            {{-- Next button --}}
            <a 
                href="{{ !$paginator->hasMorePages() ? '#' : $paginator->nextPageUrl() }}"
                @class([
                    'px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium transition-colors duration-200',
                    'hover:bg-gray-50 hover:border-gray-400 cursor-pointer' => $paginator->hasMorePages(),
                    'bg-gray-100 text-gray-400 cursor-not-allowed' => !$paginator->hasMorePages()
                ])
                @if(!$paginator->hasMorePages()) disabled @endif
            >
                Next
            </a>
        </div>
    </div>
@else
    {{-- Show only the info when no pagination needed --}}
    @if($total > 0)
        <div class="p-6 border-t border-gray-200">
            <div class="text-sm text-gray-600">
                Showing 
                <span class="font-medium">1</span> 
                to 
                <span class="font-medium">{{ $total }}</span> 
                of 
                <span class="font-medium">{{ $total }}</span> 
                @isset($label)
                    {{ $label }}
                @else
                    {{ Str::plural('item', $total) }}
                @endisset
            </div>
        </div>
    @endif
@endif