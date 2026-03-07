@if ($paginator->hasPages())
<nav class="flex items-center gap-1">
    @if ($paginator->onFirstPage())
        <span class="px-3 py-1.5 text-sm text-gray-300 dark:text-gray-600 cursor-not-allowed">←</span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}" class="px-3 py-1.5 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-stone-100 dark:hover:bg-gray-800 rounded-lg transition-colors">←</a>
    @endif
    @foreach ($elements as $element)
        @if (is_string($element))
            <span class="px-2 py-1.5 text-sm text-gray-400">{{ $element }}</span>
        @endif
        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span class="px-3 py-1.5 text-sm font-semibold bg-orange-500 text-white rounded-lg">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="px-3 py-1.5 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-stone-100 dark:hover:bg-gray-800 rounded-lg transition-colors">{{ $page }}</a>
                @endif
            @endforeach
        @endif
    @endforeach
    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" class="px-3 py-1.5 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-stone-100 dark:hover:bg-gray-800 rounded-lg transition-colors">→</a>
    @else
        <span class="px-3 py-1.5 text-sm text-gray-300 dark:text-gray-600 cursor-not-allowed">→</span>
    @endif
</nav>
@endif
