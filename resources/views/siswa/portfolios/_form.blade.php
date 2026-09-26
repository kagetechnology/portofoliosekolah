@php
    $portfolio = $portfolio ?? new \App\Models\Portfolio();
    $selectedStudents = collect($selectedStudents ?? []);
    $selectedContributorIds = collect(old('contributor_ids', $selectedStudents->pluck('id')->all()))->map(fn ($id) => (int) $id);
    $selectedStudents = $selectedStudents->filter(fn ($student) => $selectedContributorIds->contains($student->id));
    $categories = $categories ?? collect();
    $isEdit = isset($isEdit) ? $isEdit : (request()->routeIs('siswa.portfolios.edit') && $portfolio->exists);
    $action = $isEdit ? route('siswa.portfolios.update', $portfolio) : route('siswa.portfolios.store');
@endphp

<form method="POST" action="{{ $action }}"
      enctype="multipart/form-data"
      class="grid min-w-0 gap-5 lg:grid-cols-3">
    @csrf @if($isEdit) @method('PATCH') @endif

    <div class="min-w-0 space-y-5 lg:col-span-2">
        {{-- Section 1: Identitas & Tipe Karya --}}
        <section class="min-w-0 rounded-[1.75rem] border border-zinc-200 bg-white p-4 shadow-sm sm:p-6">
            <div class="mb-4 flex items-center justify-between border-b border-zinc-100 pb-3">
                <h2 class="text-sm font-semibold uppercase tracking-wider text-zinc-500">1. Identitas &amp; Jenis Karya</h2>
                <span class="text-xs text-zinc-400">* Kolom wajib</span>
            </div>
            <div class="space-y-4">
                <div class="space-y-1.5">
                    <label class="block text-sm font-medium text-zinc-800">Judul Karya <span class="text-red-500">*</span></label>
                    <input name="title" value="{{ old('title', $portfolio->title) }}" required placeholder="Contoh: Aplikasi Monitoring Inventaris Lab Berbasis IoT"
                           class="w-full rounded-lg border border-zinc-200 px-3 py-2.5 text-sm focus:border-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-900/10">
                    @error('title') <p class="text-xs font-medium text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="space-y-1.5">
                        <label class="block text-sm font-medium text-zinc-800">Kategori</label>
                        <select name="category" class="w-full rounded-lg border border-zinc-200 bg-white px-3 py-2.5 text-sm focus:border-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-900/10">
                            <option value="">Pilih kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->name }}" @selected(old('category', $portfolio->category) === $category->name)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @if ($categories->isEmpty())
                            <p class="text-xs text-amber-700">Belum ada kategori. Hubungi admin sekolah.</p>
                        @endif
                        @error('category') <p class="text-xs font-medium text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-sm font-medium text-zinc-800">Tipe Project <span class="text-red-500">*</span></label>
                        <select name="project_type" data-project-type required class="w-full rounded-lg border border-zinc-200 bg-white px-3 py-2.5 text-sm focus:border-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-900/10">
                            <option value="personal" @selected(old('project_type', $portfolio->project_type ?: 'personal') === 'personal')>Personal (Individu)</option>
                            <option value="team" @selected(old('project_type', $portfolio->project_type) === 'team')>Tim (Kolaborasi Siswa)</option>
                        </select>
                    </div>
                </div>

                {{-- Team Members Picker --}}
                <div class="min-w-0 max-w-full space-y-3 overflow-hidden rounded-xl border border-zinc-200 bg-zinc-50 p-3 sm:p-4" data-team-fields data-contributor-picker data-max="20" data-search-url="{{ route('siswa.contributors.search') }}">
                    <div class="flex flex-wrap items-start justify-between gap-2">
                        <div>
                            <label for="contributor-search" class="block text-sm font-semibold text-zinc-900">Anggota Tim</label>
                            <p class="mt-1 text-xs text-zinc-500">Cari nama atau kelas siswa, lalu centang untuk menambahkan anggota.</p>
                        </div>
                        <span class="rounded-full bg-white px-2.5 py-1 text-xs font-semibold text-zinc-600 ring-1 ring-zinc-200" data-contributor-count>0 / 20 dipilih</span>
                    </div>

                    <div class="hidden rounded-lg border border-blue-200 bg-blue-50 p-3" data-contributor-selected-wrap>
                        <p class="mb-2 text-xs font-semibold text-blue-900">Anggota terpilih</p>
                        <div class="flex min-w-0 flex-wrap gap-2" data-contributor-selected></div>
                    </div>

                    <div class="relative">
                        <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-zinc-400" />
                        <input id="contributor-search" type="search" data-contributor-search autocomplete="off" placeholder="Ketik nama atau kelas siswa..."
                               class="w-full rounded-lg border border-zinc-300 bg-white py-2.5 pl-9 pr-3 text-sm placeholder:text-zinc-500 focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600/15">
                    </div>

                    <div class="max-h-72 min-w-0 overflow-y-auto rounded-lg border border-zinc-200 bg-white" data-contributor-list>
                        @foreach ($selectedStudents as $student)
                            <div class="relative flex items-center gap-3 border-b border-zinc-100 px-3 py-2.5 transition last:border-b-0 hover:bg-blue-50 has-[:checked]:bg-blue-50" data-contributor-option>
                                <input id="contributor-{{ $student->id }}" type="checkbox" name="contributor_ids[]" value="{{ $student->id }}" checked
                                       class="h-4 w-4 shrink-0 rounded border-zinc-300 text-blue-600 focus:ring-blue-600">
                                <label for="contributor-{{ $student->id }}" class="absolute inset-0 cursor-pointer" aria-label="Pilih {{ $student->name }}"></label>
                                <span class="pointer-events-none flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-zinc-100 text-xs font-bold text-zinc-600" aria-hidden="true">{{ mb_substr($student->name, 0, 1) }}</span>
                                <span class="pointer-events-none min-w-0 flex-1">
                                    <span class="block truncate text-sm font-medium text-zinc-900" data-contributor-name>{{ $student->name }}</span>
                                    <span class="block truncate text-xs text-zinc-500">{{ $student->school_class ?: 'Kelas belum diatur' }}</span>
                                </span>
                                <span class="pointer-events-none hidden text-xs font-semibold text-blue-700" data-selected-label>Dipilih</span>
                            </div>
                        @endforeach
                        <p class="px-4 py-8 text-center text-sm text-zinc-500" data-contributor-empty>Ketik minimal 2 karakter untuk mencari siswa.</p>
                    </div>
                    <p class="text-xs text-zinc-500">Siswa yang dipilih akan menerima undangan dan harus mengakui project ini sebagai karyanya.</p>
                    <p class="hidden text-xs font-semibold text-amber-700" data-contributor-limit>Maksimal 20 anggota tim.</p>
                    @error('contributor_ids.*') <p class="text-xs font-medium text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </section>

        {{-- Section 2: Media Utama & Cover Portofolio --}}
        <section class="min-w-0 rounded-[1.75rem] border border-zinc-200 bg-white p-4 shadow-sm sm:p-6">
            <div class="mb-4 flex items-center justify-between border-b border-zinc-100 pb-3">
                <h2 class="text-sm font-semibold uppercase tracking-wider text-zinc-500">2. Media Utama (Cover Karya)</h2>
                <span class="text-xs font-medium text-zinc-500">{{ $isEdit ? 'Opsional saat edit' : '* Wajib' }}</span>
            </div>

            <div class="space-y-4">
                <div class="rounded-xl border border-zinc-200 bg-zinc-50/70 p-4 text-xs text-zinc-600">
                    <p class="font-semibold text-zinc-900">Petunjuk Gambar Cover yang Baik:</p>
                    <ul class="mt-1.5 list-disc space-y-1 pl-4 text-zinc-600">
                        <li>Gunakan rasio <strong class="font-semibold text-zinc-800">16:9</strong> atau <strong class="font-semibold text-zinc-800">4:3</strong> (resolusi rekomendasi minimal 1280 &times; 720 piksel).</li>
                        <li>Format file didukung: <strong class="font-semibold text-zinc-800">JPG, PNG, atau WebP</strong> hingga 10MB (otomatis dikompresi &amp; dioptimalkan sistem).</li>
                        <li>Gunakan screenshot antarmuka paling menarik, mockup perangkat, atau foto fisik karya dengan pencahayaan jelas.</li>
                    </ul>
                </div>

                <div class="space-y-1.5">
                    <label class="block text-sm font-medium text-zinc-800">Unggah File Cover <span class="text-red-500">{{ $isEdit ? '' : '*' }}</span></label>
                    <input type="file" name="cover_image" accept="image/jpeg,image/png,image/webp" data-image-preview-input="#portfolio-cover-preview" @required(! $isEdit)
                           class="block w-full rounded-lg border border-zinc-200 bg-white p-2.5 text-sm file:mr-3 file:rounded-md file:border-0 file:bg-zinc-900 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-white hover:file:bg-zinc-800">
                    @error('cover_image') <p class="text-xs font-medium text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </section>

        {{-- Section 3: Dokumentasi & Deskripsi Lengkap --}}
        <section class="min-w-0 rounded-[1.75rem] border border-zinc-200 bg-white p-4 shadow-sm sm:p-6">
            <div class="mb-4 flex flex-wrap items-center justify-between gap-2 border-b border-zinc-100 pb-3">
                <div>
                    <h2 class="text-sm font-semibold uppercase tracking-wider text-zinc-500">3. Dokumentasi &amp; Deskripsi Lengkap</h2>
                    <p class="mt-0.5 text-xs text-zinc-400">Jelaskan karya secara komprehensif agar menarik perhatian penguji &amp; perusahaan.</p>
                </div>
                <x-badge variant="danger">* Wajib · Min. 300 Karakter</x-badge>
            </div>

            <div class="space-y-2">
                <x-rich-editor name="description" :value="old('description', $portfolio->description)" :required="true" rows="10" :minlength="300" :showGuidance="true"
                               placeholder="Ceritakan proses pembuatan karya, fitur utama, peran dalam tim, dan sematkan screenshot atau video demo..." />
                @error('description') <p class="text-xs font-medium text-red-600">{{ $message }}</p> @enderror
            </div>
        </section>

        {{-- Section 4: Tautan & Demo Interaktif --}}
        <section class="min-w-0 rounded-[1.75rem] border border-zinc-200 bg-white p-4 shadow-sm sm:p-6">
            <div class="mb-4 border-b border-zinc-100 pb-3">
                <h2 class="text-sm font-semibold uppercase tracking-wider text-zinc-500">4. Tautan Publik &amp; Demo</h2>
                <p class="mt-0.5 text-xs text-zinc-400">Tautan ini memudahkan guru dan perusahaan menguji langsung karya Anda.</p>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="space-y-1.5">
                    <label class="block text-sm font-medium text-zinc-800">
                        URL Demo / Website / Prototype
                    </label>
                    <input name="project_url" type="url" value="{{ old('project_url', $portfolio->project_url) }}" placeholder="https://demo-karya.com atau link Figma/video"
                           class="w-full rounded-lg border border-zinc-200 px-3 py-2.5 text-sm focus:border-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-900/10">
                    <p class="text-[11px] text-zinc-500">Bisa berupa link live website, demo APK, prototype Figma, atau Google Drive publik.</p>
                    @error('project_url') <p class="text-xs font-medium text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-1.5">
                    <label class="block text-sm font-medium text-zinc-800">
                        URL Repository GitHub / GitLab
                    </label>
                    <input name="github_url" type="url" value="{{ old('github_url', $portfolio->github_url) }}" placeholder="https://github.com/username/repository"
                           class="w-full rounded-lg border border-zinc-200 px-3 py-2.5 text-sm focus:border-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-900/10">
                    <p class="text-[11px] text-zinc-500">Tautan source code publik agar recruiter teknis dapat melihat struktur kode Anda.</p>
                    @error('github_url') <p class="text-xs font-medium text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </section>
    </div>

    <div class="min-w-0 space-y-5">
        <section class="overflow-hidden rounded-[1.75rem] border border-zinc-200 bg-white shadow-sm">
            <div class="aspect-[4/3] bg-zinc-100">
                <img id="portfolio-cover-preview" src="{{ $portfolio->cover_image ? asset('storage/'.$portfolio->cover_image) : asset('img/placeholder-portfolio.svg') }}"
                     alt="" class="h-full w-full object-cover">
            </div>
        </section>

        <section class="rounded-[1.75rem] border border-blue-200 bg-blue-50 p-5">
            <h2 class="text-sm font-semibold text-blue-950">Penilaian Skill</h2>
            <p class="mt-2 text-xs leading-5 text-blue-800">Skill dan level ditetapkan oleh guru atau admin setelah portofolio ditinjau.</p>
            @if ($isEdit && $portfolio->skills->count())
                <div class="mt-3 flex flex-wrap gap-2">
                    @foreach ($portfolio->skills as $skill)
                        <span class="rounded-full bg-white px-3 py-1.5 text-xs font-semibold text-blue-800 ring-1 ring-blue-200">{{ $skill->name }} · Lv {{ $skill->pivot->level }}</span>
                    @endforeach
                </div>
            @endif
        </section>

        <div class="flex flex-col gap-2">
            <x-button type="submit" variant="primary" size="lg">
                <x-icon name="check" class="h-4 w-4" /> {{ $isEdit ? 'Simpan Perubahan' : 'Publish Portofolio' }}
            </x-button>
            <a href="{{ route('siswa.portfolios.index') }}" class="rounded-lg border border-zinc-300 px-4 py-2.5 text-center text-sm font-medium text-zinc-700 hover:bg-zinc-50">Batal</a>
        </div>
    </div>
</form>
