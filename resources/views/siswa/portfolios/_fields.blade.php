@php
    $p = $portfolio ?? new \App\Models\Portfolio();
    $selectedSkills = $selectedSkills ?? [];
    $selectedLevels = $selectedLevels ?? [];
@endphp

<div class="grid gap-4 md:grid-cols-2">
    <div class="md:col-span-2">
        <label class="block text-sm font-medium">Judul *</label>
        <input name="title" value="{{ old('title', $p->title) }}" required class="mt-1 w-full rounded border-slate-300 px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium">Kategori</label>
        <input name="category" value="{{ old('category', $p->category) }}" placeholder="Web, Mobile, IoT, dst." class="mt-1 w-full rounded border-slate-300 px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium">Cover Image (max 2MB)</label>
        <input type="file" name="cover_image" accept="image/*" class="mt-1 text-sm">
        @if ($p->cover_image)
            <img src="{{ asset('storage/'.$p->cover_image) }}" class="mt-2 h-20 rounded object-cover">
        @endif
    </div>
    <div>
        <label class="block text-sm font-medium">Project URL</label>
        <input name="project_url" value="{{ old('project_url', $p->project_url) }}" type="url" class="mt-1 w-full rounded border-slate-300 px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium">GitHub URL</label>
        <input name="github_url" value="{{ old('github_url', $p->github_url) }}" type="url" class="mt-1 w-full rounded border-slate-300 px-3 py-2 text-sm">
    </div>
    <div class="md:col-span-2">
        <label class="block text-sm font-medium">Deskripsi *</label>
        <textarea name="description" required rows="5" class="mt-1 w-full rounded border-slate-300 px-3 py-2 text-sm">{{ old('description', $p->description) }}</textarea>
    </div>
    <label class="flex items-center gap-2 text-sm">
        <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $p->is_featured))>
        Tampilkan di beranda (Unggulan)
    </label>
</div>

<fieldset class="mt-6 rounded border border-slate-200 p-4">
    <legend class="px-2 text-sm font-medium">Skills</legend>
    <div id="skill-list" class="space-y-2">
        @forelse ($skills as $skill)
            @php $checked = in_array($skill->id, $selectedSkills); @endphp
            <div class="flex items-center gap-3 text-sm">
                <label class="flex flex-1 items-center gap-2">
                    <input type="checkbox" name="skill_names[]" value="{{ $skill->name }}" @checked($checked)>
                    {{ $skill->name }}
                </label>
                <select name="skill_levels[{{ $skill->id }}]" class="rounded border-slate-300 px-2 py-1 text-xs">
                    @for ($i = 1; $i <= 5; $i++)
                        <option value="{{ $i }}" @selected(($selectedLevels[$skill->id] ?? 0) == $i)>{{ $i }}</option>
                    @endfor
                </select>
            </div>
        @empty
            <p class="text-sm text-slate-400">Belum ada skill di master. Tambah di bawah.</p>
        @endforelse
    </div>
    <details class="mt-3 text-sm">
        <summary class="cursor-pointer text-brand-600">+ Tambah skill baru</summary>
        <div id="new-skill-rows" class="mt-2 space-y-1">
            <input name="skill_names[]" placeholder="contoh: React" class="rounded border-slate-300 px-3 py-1 text-sm">
            <input name="skill_names[]" placeholder="contoh: Laravel" class="rounded border-slate-300 px-3 py-1 text-sm">
        </div>
    </details>
</fieldset>
