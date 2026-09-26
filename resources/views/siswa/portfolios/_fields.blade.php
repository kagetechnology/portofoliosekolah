@php
    $p = $portfolio ?? new \App\Models\Portfolio();
@endphp

<div class="grid gap-4 md:grid-cols-2">
    <div class="md:col-span-2">
        <label class="block text-sm font-medium">Judul *</label>
        <input name="title" value="{{ old('title', $p->title) }}" required class="mt-1 w-full rounded border-slate-300 px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium">Kategori</label>
        <select name="category" class="mt-1 w-full rounded border-slate-300 px-3 py-2 text-sm">
            <option value="">Pilih kategori</option>
            @foreach (($categories ?? collect()) as $category)
                <option value="{{ $category->name }}" @selected(old('category', $p->category) === $category->name)>{{ $category->name }}</option>
            @endforeach
        </select>
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
