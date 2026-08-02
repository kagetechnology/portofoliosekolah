@php
    $p = $p ?? $portfolio;
@endphp
<a href="{{ route('portfolios.show', $p) }}" class="block rounded border border-slate-200 bg-white p-3 transition hover:shadow">
    <div class="aspect-video overflow-hidden rounded bg-slate-100">
        <img src="{{ $p->coverUrl() }}" alt="" class="h-full w-full object-cover">
    </div>
    <p class="mt-2 font-semibold">{{ $p->title }}</p>
    <p class="text-xs text-slate-500">{{ $p->user->name }} @if($p->category) · {{ $p->category }} @endif</p>
    @if ($p->skills->count())
        <div class="mt-2 flex flex-wrap gap-1">
            @foreach ($p->skills->take(3) as $s)
                <span class="rounded bg-slate-100 px-2 py-0.5 text-xs">{{ $s->name }}</span>
            @endforeach
        </div>
    @endif
</a>
