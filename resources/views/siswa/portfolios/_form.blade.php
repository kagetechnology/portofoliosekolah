@php
    $portfolio = $portfolio ?? new \App\Models\Portfolio();
    $selectedSkills = $selectedSkills ?? [];
    $selectedLevels = $selectedLevels ?? [];
    $skills = $skills ?? \App\Models\Skill::orderBy('name')->get();
    $isEdit = isset($isEdit) ? $isEdit : (request()->routeIs('siswa.portfolios.edit') && $portfolio->exists);
    $action = $isEdit ? route('siswa.portfolios.update', $portfolio) : route('siswa.portfolios.store');
@endphp

<form method="POST" action="{{ $action }}"
      enctype="multipart/form-data"
      class="grid gap-5 lg:grid-cols-3">
    @csrf @if($isEdit) @method('PATCH') @endif

    <div class="space-y-5 lg:col-span-2">
        <section class="rounded-2xl border border-zinc-200 bg-white p-6">
            <h2 class="mb-4 text-sm font-semibold uppercase tracking-wider text-zinc-500">Detail</h2>
            <div class="space-y-4">
                <div class="space-y-1.5">
                    <label class="block text-sm font-medium text-zinc-800">Judul <span class="text-red-500">*</span></label>
                    <input name="title" value="{{ old('title', $portfolio->title) }}" required
                           class="w-full rounded-lg border border-zinc-200 px-3 py-2.5 text-sm focus:border-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-900/10">
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="space-y-1.5">
                        <label class="block text-sm font-medium text-zinc-800">Kategori</label>
                        <input name="category" value="{{ old('category', $portfolio->category) }}" placeholder="Web, Mobile, IoT, dsb."
                               class="w-full rounded-lg border border-zinc-200 px-3 py-2.5 text-sm focus:border-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-900/10">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-sm font-medium text-zinc-800">Cover Image</label>
                        <input type="file" name="cover_image" accept="image/*" data-image-preview-input="#portfolio-cover-preview"
                               class="block w-full text-sm file:mr-3 file:rounded-md file:border-0 file:bg-zinc-900 file:px-3 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-zinc-800">
                    </div>
                </div>
                <div class="space-y-1.5">
                    <label class="block text-sm font-medium text-zinc-800">Deskripsi <span class="text-red-500">*</span></label>
                    <x-rich-editor name="description" :value="old('description', $portfolio->description)" :required="true" rows="8" placeholder="Ceritakan detail karya, proses, hasil, link media, atau dokumentasi..." />
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="space-y-1.5">
                        <label class="block text-sm font-medium text-zinc-800">Project URL</label>
                        <input name="project_url" type="url" value="{{ old('project_url', $portfolio->project_url) }}" placeholder="https://demo.com"
                               class="w-full rounded-lg border border-zinc-200 px-3 py-2.5 text-sm focus:border-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-900/10">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-sm font-medium text-zinc-800">GitHub URL</label>
                        <input name="github_url" type="url" value="{{ old('github_url', $portfolio->github_url) }}" placeholder="https://github.com/..."
                               class="w-full rounded-lg border border-zinc-200 px-3 py-2.5 text-sm focus:border-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-900/10">
                    </div>
                </div>
</div>
            </section>
    </div>

    <div class="space-y-5">
        <section class="overflow-hidden rounded-2xl border border-zinc-200 bg-white">
            <div class="aspect-[4/3] bg-zinc-100">
                <img id="portfolio-cover-preview" src="{{ $portfolio->cover_image ? asset('storage/'.$portfolio->cover_image) : asset('img/placeholder-portfolio.svg') }}"
                     alt="" class="h-full w-full object-cover">
            </div>
        </section>

        <section class="rounded-2xl border border-zinc-200 bg-white p-5">
            <h2 class="mb-3 text-sm font-semibold uppercase tracking-wider text-zinc-500">Tech & Skill</h2>
            @if ($skills->count())
                <div class="space-y-2">
                    @foreach ($skills as $skill)
                        @php $checked = in_array($skill->id, $selectedSkills); @endphp
                        <div class="flex items-center justify-between gap-2 rounded-lg border border-zinc-200 px-3 py-2">
                            <label class="flex flex-1 items-center gap-2 text-sm">
                                <input type="checkbox" name="skill_names[]" value="{{ $skill->name }}" @checked($checked) class="h-4 w-4 rounded border-zinc-300">
                                {{ $skill->name }}
                            </label>
                            <select name="skill_levels[{{ $skill->id }}]" class="rounded-md border border-zinc-200 px-2 py-1 text-xs">
                                @for ($i = 1; $i <= 5; $i++)
                                    <option value="{{ $i }}" @selected(($selectedLevels[$skill->id] ?? 0) == $i)>Lv {{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-xs text-zinc-500">Belum ada skill master.</p>
            @endif

            <details class="mt-4 rounded-lg border border-dashed border-zinc-300 p-3 text-sm">
                <summary class="cursor-pointer text-blue-600">+ Tambah skill baru</summary>
                <div class="mt-3 space-y-2">
                    <input name="skill_names[]" placeholder="contoh: React" class="w-full rounded-md border border-zinc-200 px-3 py-1.5 text-sm">
                    <input name="skill_names[]" placeholder="contoh: Laravel" class="w-full rounded-md border border-zinc-200 px-3 py-1.5 text-sm">
                    <input name="skill_names[]" placeholder="contoh: Figma" class="w-full rounded-md border border-zinc-200 px-3 py-1.5 text-sm">
                </div>
            </details>
        </section>

        <div class="flex flex-col gap-2">
            <x-button type="submit" variant="primary" size="lg">
                <x-icon name="check" class="h-4 w-4" /> {{ $isEdit ? 'Simpan Perubahan' : 'Publish Portofolio' }}
            </x-button>
            <a href="{{ route('siswa.portfolios.index') }}" class="rounded-lg border border-zinc-300 px-4 py-2.5 text-center text-sm font-medium text-zinc-700 hover:bg-zinc-50">Batal</a>
        </div>
    </div>
</form>
