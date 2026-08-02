@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination" class="flex items-center justify-center gap-1 text-sm">
        @if ($paginator->onFirstPage())
            <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-zinc-200 text-zinc-300">
                <x-icon name="arrow-left" class="h-4 w-4 rotate-180" />
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
               class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-zinc-200 text-zinc-600 transition hover:bg-zinc-50">
                <x-icon name="arrow-left" class="h-4 w-4" />
            </a>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="px-2 text-zinc-400">…</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span aria-current="page"
                              class="inline-flex h-9 min-w-9 items-center justify-center rounded-lg bg-zinc-900 px-3 text-white">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}"
                           class="inline-flex h-9 min-w-9 items-center justify-center rounded-lg border border-zinc-200 px-3 text-zinc-700 transition hover:bg-zinc-50">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next"
               class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-zinc-200 text-zinc-600 transition hover:bg-zinc-50">
                <x-icon name="arrow-right" class="h-4 w-4" />
            </a>
        @else
            <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-zinc-200 text-zinc-300">
                <x-icon name="arrow-right" class="h-4 w-4" />
            </span>
        @endif
    </nav>
@endif
