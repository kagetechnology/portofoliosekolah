@extends('layouts.admin')
@section('title', 'Edit Portofolio')

@section('content')
    <header class="mb-6 rounded-[2rem] border border-zinc-200 bg-white p-6 shadow-sm">
        <a href="{{ route($routePrefix.'.portfolios.show', $portfolio) }}" class="inline-flex items-center gap-2 text-sm text-zinc-600 hover:text-blue-600">
            <x-icon name="arrow-left" class="h-4 w-4" /> Kembali ke review
        </a>
        <div class="mt-3 flex flex-wrap items-end justify-between gap-3">
            <div>
                <h1 class="font-display text-4xl font-bold tracking-tight text-zinc-950 md:text-5xl">Edit Portofolio</h1>
                <p class="mt-2 text-sm text-zinc-500">Perbaiki detail karya dan tetapkan skill berdasarkan hasil review.</p>
            </div>
            <x-badge :variant="$routePrefix === 'guru' ? 'info' : 'neutral'">{{ $routePrefix === 'guru' ? 'Guru' : 'Admin' }}</x-badge>
        </div>
    </header>

    <form method="POST" action="{{ route($routePrefix.'.portfolios.update', $portfolio) }}" enctype="multipart/form-data" class="grid min-w-0 gap-5 lg:grid-cols-[minmax(0,1fr)_22rem]">
        @csrf @method('PUT')

        <div class="min-w-0 space-y-5">
            <section class="rounded-[1.75rem] border border-zinc-200 bg-white p-5 shadow-sm sm:p-6">
                <h2 class="mb-4 text-sm font-semibold uppercase tracking-wider text-zinc-500">Detail Karya</h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-zinc-800">Judul <span class="text-red-500">*</span></label>
                        <input name="title" value="{{ old('title', $portfolio->title) }}" required class="mt-1.5 w-full rounded-lg border border-zinc-200 px-3 py-2.5 text-sm focus:border-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-900/10">
                        @error('title') <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-zinc-800">Kategori</label>
                            <select name="category" class="mt-1.5 w-full rounded-lg border border-zinc-200 bg-white px-3 py-2.5 text-sm">
                                <option value="">Pilih kategori</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->name }}" @selected(old('category', $portfolio->category) === $category->name)>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-zinc-800">Tipe Project</label>
                            <select name="project_type" required class="mt-1.5 w-full rounded-lg border border-zinc-200 bg-white px-3 py-2.5 text-sm">
                                <option value="personal" @selected(old('project_type', $portfolio->project_type) === 'personal')>Personal</option>
                                <option value="team" @selected(old('project_type', $portfolio->project_type) === 'team')>Tim</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center justify-between gap-2">
                            <label class="block text-sm font-medium text-zinc-800">Deskripsi Karya <span class="text-red-500">* (Wajib, min. 300 karakter)</span></label>
                            <span class="text-xs text-zinc-400">Review &amp; rapikan dokumentasi siswa</span>
                        </div>
                        <div class="mt-1.5"><x-rich-editor name="description" :value="old('description', $portfolio->description)" :required="true" rows="10" :minlength="300" :showGuidance="true" /></div>
                        @error('description') <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-zinc-800">Project URL</label>
                            <input name="project_url" type="url" value="{{ old('project_url', $portfolio->project_url) }}" class="mt-1.5 w-full rounded-lg border border-zinc-200 px-3 py-2.5 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-zinc-800">GitHub URL</label>
                            <input name="github_url" type="url" value="{{ old('github_url', $portfolio->github_url) }}" class="mt-1.5 w-full rounded-lg border border-zinc-200 px-3 py-2.5 text-sm">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-zinc-800">Ganti Cover</label>
                        <input type="file" name="cover_image" accept="image/jpeg,image/png,image/webp" class="mt-1.5 block w-full text-sm file:mr-3 file:rounded-md file:border-0 file:bg-zinc-900 file:px-3 file:py-2 file:text-sm file:font-medium file:text-white">
                        <p class="mt-1 text-xs text-zinc-500">Kosongkan untuk mempertahankan cover saat ini.</p>
                    </div>
                </div>
            </section>
        </div>

        <aside class="min-w-0 space-y-5">
            <section class="overflow-hidden rounded-[1.75rem] border border-zinc-200 bg-white shadow-sm">
                <img src="{{ $portfolio->coverUrl() }}" alt="" class="aspect-[4/3] w-full object-cover">
                <div class="p-4">
                    <p class="text-xs text-zinc-500">Pemilik</p>
                    <p class="mt-1 text-sm font-semibold text-zinc-900">{{ $portfolio->user->name }}</p>
                </div>
            </section>

            <section class="rounded-[1.75rem] border border-blue-200 bg-blue-50 p-5">
                <h2 class="text-sm font-bold text-blue-950">Penilaian Skill</h2>
                <p class="mt-1 text-xs leading-5 text-blue-800">Skill hanya dapat ditetapkan oleh guru atau admin setelah meninjau karya.</p>
                <div class="mt-4 max-h-[28rem] space-y-2 overflow-y-auto pr-1">
                    @forelse ($skills as $skill)
                        @php $checked = in_array($skill->id, old('skill_ids', $selectedSkills)); @endphp
                        <div class="rounded-lg border border-blue-200 bg-white p-3">
                            <label class="flex items-center gap-2 text-sm font-medium text-zinc-900">
                                <input type="checkbox" name="skill_ids[]" value="{{ $skill->id }}" @checked($checked) class="h-4 w-4 rounded border-zinc-300 text-blue-600">
                                <span class="min-w-0 flex-1 truncate">{{ $skill->name }}</span>
                            </label>
                            <select name="skill_levels[{{ $skill->id }}]" class="mt-2 w-full rounded-md border border-zinc-200 px-2 py-1.5 text-xs">
                                @for ($level = 1; $level <= 5; $level++)
                                    <option value="{{ $level }}" @selected(old('skill_levels.'.$skill->id, $selectedLevels[$skill->id] ?? 1) == $level)>Level {{ $level }}</option>
                                @endfor
                            </select>
                        </div>
                    @empty
                        <p class="text-xs text-blue-800">Belum ada master skill.</p>
                    @endforelse
                </div>
            </section>

            <div class="flex flex-col gap-2">
                <x-button type="submit" variant="primary" size="lg"><x-icon name="check" class="h-4 w-4" /> Simpan Portofolio</x-button>
                <a href="{{ route($routePrefix.'.portfolios.show', $portfolio) }}" class="rounded-lg border border-zinc-300 bg-white px-4 py-2.5 text-center text-sm font-medium text-zinc-700 hover:bg-zinc-50">Batal</a>
            </div>
        </aside>
    </form>
@endsection
