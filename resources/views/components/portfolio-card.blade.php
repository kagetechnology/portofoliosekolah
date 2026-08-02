@props(['portfolio', 'featured' => false])

<a href="{{ route('portfolios.show', $portfolio) }}"
   class="group relative block overflow-hidden rounded-2xl border border-zinc-200 bg-white transition duration-200 hover:border-zinc-300 hover:shadow-xl hover:-translate-y-0.5">

    <div class="relative aspect-[4/3] overflow-hidden bg-zinc-100">
        <img src="{{ $portfolio->coverUrl() }}" alt="{{ $portfolio->title }}"
             class="h-full w-full object-cover transition duration-500 ease-out group-hover:scale-105" />
        <div class="absolute inset-x-0 bottom-0 h-20 bg-gradient-to-t from-black/40 to-transparent"></div>
        @if ($featured)
            <span class="absolute left-3 top-3 inline-flex items-center gap-1 rounded-full bg-white/95 px-2 py-1 text-xs font-medium text-zinc-800 backdrop-blur">
                <x-icon name="star" class="h-3 w-3 text-amber-500" /> Unggulan
            </span>
        @endif
        @if ($portfolio->category)
            <span class="absolute right-3 top-3 rounded-full bg-white/95 px-2 py-1 text-xs font-medium text-zinc-700 backdrop-blur">
                {{ $portfolio->category }}
            </span>
        @endif
    </div>

    <div class="p-5">
        <div class="flex items-center justify-between gap-2 text-xs text-zinc-500">
            <div class="flex items-center gap-2">
                <x-icon name="user" class="h-3 w-3" />
                <span class="truncate">{{ $portfolio->user->name }}</span>
            </div>
            <span class="inline-flex items-center gap-1 text-zinc-400" title="{{ $portfolio->views }} kali dilihat">
                <x-icon name="eye" class="h-3 w-3" /> {{ $portfolio->views ?: 0 }}
            </span>
        </div>
        <h3 class="mt-2 text-base font-semibold leading-snug text-zinc-900 line-clamp-2 group-hover:text-blue-600 transition">
            {{ $portfolio->title }}
        </h3>

        @if ($portfolio->skills->count())
            <div class="mt-3 flex flex-wrap gap-1">
                @foreach ($portfolio->skills->take(3) as $skill)
                    <span class="rounded-md bg-zinc-100 px-2 py-0.5 text-xs text-zinc-600">{{ $skill->name }}</span>
                @endforeach
                @if ($portfolio->skills->count() > 3)
                    <span class="rounded-md bg-zinc-50 px-2 py-0.5 text-xs text-zinc-500">+{{ $portfolio->skills->count() - 3 }}</span>
                @endif
            </div>
        @endif

        <div class="mt-4 flex items-center text-xs font-medium text-zinc-400 transition group-hover:text-blue-600">
            <span>Lihat detail</span>
            <x-icon name="arrow-right" class="ml-1 h-3 w-3 transition group-hover:translate-x-1" />
        </div>
    </div>
</a>
