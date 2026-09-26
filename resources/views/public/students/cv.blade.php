<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="index,follow">
    <title>CV {{ $user->name }}</title>
    @vite(['resources/css/app.css'])
    <style>
        body { font-family: 'Archivo', system-ui, sans-serif; }
        @page { size: A4; margin: 12mm; }
        @media print {
            body { background: white !important; }
            .cv-toolbar { display: none !important; }
            .cv-page { width: auto !important; min-height: auto !important; margin: 0 !important; box-shadow: none !important; }
            a { color: inherit !important; text-decoration: none !important; }
            .avoid-break { break-inside: avoid; }
        }
    </style>
</head>
<body class="bg-zinc-100 font-sans text-zinc-900 antialiased">
    <div class="cv-toolbar sticky top-0 z-10 border-b border-zinc-200 bg-white/95 px-4 py-3 backdrop-blur">
        <div class="mx-auto flex max-w-[210mm] flex-wrap items-center justify-between gap-3">
            <div>
                <p class="text-sm font-semibold text-zinc-900">CV otomatis {{ $user->name }}</p>
                <p class="text-xs text-zinc-500">Gunakan dialog print untuk memilih “Save as PDF”.</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('portfolios.user', $user) }}" class="rounded-lg border border-zinc-300 px-4 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-50">Profil publik</a>
                <button type="button" onclick="window.print()" class="rounded-lg bg-zinc-950 px-4 py-2 text-sm font-semibold text-white hover:bg-zinc-800">Print / Simpan PDF</button>
            </div>
        </div>
    </div>

    <main class="cv-page mx-auto my-8 min-h-[297mm] w-full max-w-[210mm] bg-white p-8 shadow-sm sm:p-12">
        <header class="grid gap-6 border-b-2 border-zinc-900 pb-7 sm:grid-cols-[6rem_minmax(0,1fr)] sm:items-center">
            <img src="{{ $user->avatarUrl() }}" alt="{{ $user->name }}" class="h-24 w-24 rounded-xl object-cover ring-1 ring-zinc-200">
            <div>
                <p class="text-sm font-semibold text-blue-700">{{ $user->school_class ?: 'Siswa' }}{{ $school?->name ? ' · '.$school->name : '' }}</p>
                <h1 class="mt-1 text-3xl font-bold tracking-tight text-zinc-950 sm:text-4xl">{{ $user->name }}</h1>
                <div class="mt-3 flex flex-wrap gap-x-4 gap-y-1 text-sm text-zinc-600">
                    @if ($user->email)<a href="mailto:{{ $user->email }}">{{ $user->email }}</a>@endif
                    @if ($user->phone)<a href="tel:{{ $user->phone }}">{{ $user->phone }}</a>@endif
                    @if ($user->github_url)<a href="{{ $user->github_url }}">GitHub</a>@endif
                    @if ($user->linkedin_url)<a href="{{ $user->linkedin_url }}">LinkedIn</a>@endif
                </div>
            </div>
        </header>

        @if ($user->bio)
            <section class="mt-7 avoid-break">
                <h2 class="text-sm font-bold uppercase tracking-wider text-zinc-950">Profil</h2>
                <div class="rich-content mt-2 max-w-[75ch] text-sm leading-6 text-zinc-700">{!! \App\Support\RichText::clean($user->bio) !!}</div>
            </section>
        @endif

        @if ($skills->count())
            <section class="mt-7 avoid-break">
                <h2 class="text-sm font-bold uppercase tracking-wider text-zinc-950">Keahlian</h2>
                <div class="mt-3 flex flex-wrap gap-2">
                    @foreach ($skills as $skill)
                        <span class="rounded-md border border-zinc-300 px-2.5 py-1 text-xs font-medium text-zinc-700">{{ $skill->name }} · Lv {{ $skill->level }}/5</span>
                    @endforeach
                </div>
            </section>
        @endif

        <section class="mt-8">
            <div class="flex items-end justify-between border-b border-zinc-300 pb-2">
                <h2 class="text-sm font-bold uppercase tracking-wider text-zinc-950">Pengalaman Project</h2>
                <span class="text-xs text-zinc-500">{{ $portfolios->count() }} karya terverifikasi</span>
            </div>
            <div class="divide-y divide-zinc-200">
                @forelse ($portfolios as $portfolio)
                    <article class="grid gap-2 py-4 avoid-break sm:grid-cols-[minmax(0,1fr)_auto]">
                        <div>
                            <h3 class="font-bold text-zinc-950">{{ $portfolio->title }}</h3>
                            <p class="mt-1 text-xs font-medium text-zinc-500">{{ $portfolio->category ?: 'Project' }} · {{ $portfolio->project_type === 'team' ? 'Tim' : 'Personal' }} · {{ $portfolio->created_at->isoFormat('MMM Y') }}</p>
                            <div class="rich-content mt-2 text-sm leading-5 text-zinc-700">{!! \Illuminate\Support\Str::limit(strip_tags(\App\Support\RichText::clean($portfolio->description)), 240) !!}</div>
                            @if ($portfolio->skills->count())
                                <p class="mt-2 text-xs text-zinc-600">{{ $portfolio->skills->pluck('name')->join(' · ') }}</p>
                            @endif
                        </div>
                        <a href="{{ route('portfolios.show', $portfolio) }}" class="text-xs font-semibold text-blue-700">Lihat karya</a>
                    </article>
                @empty
                    <p class="py-5 text-sm text-zinc-500">Belum ada karya yang disetujui.</p>
                @endforelse
            </div>
        </section>

        @if ($certificates->count())
            <section class="mt-8">
                <div class="flex items-end justify-between border-b border-zinc-300 pb-2">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-zinc-950">Sertifikasi</h2>
                    <span class="text-xs text-zinc-500">{{ $certificates->count() }} terverifikasi</span>
                </div>
                <div class="grid gap-x-8 sm:grid-cols-2">
                    @foreach ($certificates as $certificate)
                        <article class="py-4 avoid-break">
                            <h3 class="text-sm font-bold text-zinc-950">{{ $certificate->title }}</h3>
                            <p class="mt-1 text-xs text-zinc-600">{{ $certificate->issuer ?: 'Penerbit tidak dicantumkan' }}{{ $certificate->issue_date ? ' · '.$certificate->issue_date->isoFormat('MMM Y') : '' }}</p>
                        </article>
                    @endforeach
                </div>
            </section>
        @endif

        <footer class="mt-10 border-t border-zinc-200 pt-4 text-xs leading-5 text-zinc-500">
            CV dibuat otomatis dari data terverifikasi di {{ $school?->name ?? config('app.name') }}.
            Profil: {{ route('portfolios.user', $user) }}
        </footer>
    </main>
</body>
</html>
