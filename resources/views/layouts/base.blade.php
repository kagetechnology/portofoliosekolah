<!DOCTYPE html>
<html lang="id" class="h-full bg-zinc-50 antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#18181b">
    <title>@yield('title', 'Portofolio Sekolah')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        // === Vanilla JS sidebar (no Alpine dependency) ===
        (function () {
            function initSidebar(root) {
                var sidebar = root.querySelector('[data-sidebar]');
                var backdrop = root.querySelector('[data-sidebar-backdrop]');
                var toggles = root.querySelectorAll('[data-sidebar-toggle]');
                var closers = root.querySelectorAll('[data-sidebar-close]');
                var navLinks = root.querySelectorAll('[data-sidebar-nav] a');
                if (!sidebar) return;

                var desktop = window.innerWidth >= 1024;

                function open() {
                    sidebar.classList.remove('-translate-x-full');
                    sidebar.classList.add('translate-x-0');
                    if (backdrop) {
                        backdrop.classList.remove('opacity-0', 'pointer-events-none');
                        backdrop.classList.add('opacity-100');
                    }
                    document.body.style.overflow = 'hidden';
                    syncState();
                }
                function close() {
                    sidebar.classList.add('-translate-x-full');
                    sidebar.classList.remove('translate-x-0');
                    if (backdrop) {
                        backdrop.classList.add('opacity-0', 'pointer-events-none');
                        backdrop.classList.remove('opacity-100');
                    }
                    document.body.style.overflow = '';
                    syncState();
                }
                function syncState() {
                    var open = !sidebar.classList.contains('-translate-x-full');
                    toggles.forEach(function (t) {
                        t.setAttribute('aria-expanded', open ? 'true' : 'false');
                    });
                }

                function applyViewport() {
                    desktop = window.innerWidth >= 1024;
                    if (desktop) {
                        sidebar.classList.remove('-translate-x-full');
                        sidebar.classList.add('translate-x-0');
                        if (backdrop) {
                            backdrop.classList.add('opacity-0', 'pointer-events-none');
                            backdrop.classList.remove('opacity-100');
                        }
                        document.body.style.overflow = '';
                    } else {
                        sidebar.classList.add('-translate-x-full');
                        sidebar.classList.remove('translate-x-0');
                    }
                    syncState();
                }

                toggles.forEach(function (t) { t.addEventListener('click', function () { open(); }); });
                closers.forEach(function (c) { c.addEventListener('click', function () { close(); }); });
                if (backdrop) backdrop.addEventListener('click', close);
                navLinks.forEach(function (a) { a.addEventListener('click', function () { if (!desktop) setTimeout(close, 50); }); });

                var resizeTimer;
                window.addEventListener('resize', function () {
                    clearTimeout(resizeTimer);
                    resizeTimer = setTimeout(applyViewport, 100);
                });

                applyViewport();
            }

            // Nav (public)
            function initPublicNav() {
                var nav = document.querySelector('[data-public-nav]');
                if (!nav) return;
                var toggles = nav.querySelectorAll('[data-public-nav-toggle]');
                var closers = nav.querySelectorAll('[data-public-nav-close]');
                var menu = nav.querySelector('[data-public-nav-menu]');
                var links = nav.querySelectorAll('[data-public-nav-link]');
                if (!menu) return;

                function open() {
                    menu.classList.remove('hidden');
                    menu.classList.add('block');
                }
                function close() {
                    menu.classList.add('hidden');
                    menu.classList.remove('block');
                }
                toggles.forEach(function (t) { t.addEventListener('click', function () { open(); }); });
                closers.forEach(function (c) { c.addEventListener('click', close); });
                links.forEach(function (a) { a.addEventListener('click', close); });

                var resizeTimer;
                window.addEventListener('scroll', function () {
                    nav.classList.toggle('bg-white/85', window.scrollY > 8);
                    nav.classList.toggle('backdrop-blur', window.scrollY > 8);
                    nav.classList.toggle('shadow-sm', window.scrollY > 8);
                    nav.classList.toggle('border-zinc-200/80', window.scrollY > 8);
                }, { passive: true });
            }

            function boot() {
                document.querySelectorAll('[data-sidebar-root]').forEach(initSidebar);
                initPublicNav();
                initRichEditors();
                initImagePreviews();
                initProjectTypes();
                initContributorPickers();
                initShareButtons();
            }

            function initRichEditors() {
                var textareas = document.querySelectorAll('textarea[data-ckeditor]');
                if (!textareas.length) return;

                function start() {
                    textareas.forEach(function (textarea) {
                        if (textarea.dataset.ready) return;
                        textarea.dataset.ready = '1';

                        var wrapper = textarea.closest('[data-rich-editor-wrapper]');
                        var minLength = wrapper ? Number(wrapper.dataset.minLength || 0) : 0;
                        var counterEl = wrapper ? wrapper.querySelector('[data-char-count]') : null;
                        var counterStatusEl = wrapper ? wrapper.querySelector('[data-counter-status]') : null;
                        var templateBtn = wrapper ? wrapper.querySelector('[data-insert-template]') : null;
                        var activeEditor = null;

                        function countPlainChars(html) {
                            var tmp = document.createElement('div');
                            tmp.innerHTML = html || '';
                            var text = (tmp.textContent || tmp.innerText || '').replace(/[\s\u00a0]+/g, ' ').trim();
                            return text.length;
                        }

                        function updateCount() {
                            if (!activeEditor) return;
                            var data = activeEditor.getData();
                            var len = countPlainChars(data);
                            if (counterEl) {
                                counterEl.textContent = len;
                            }
                            if (counterStatusEl && minLength > 0) {
                                if (len >= minLength) {
                                    counterStatusEl.innerHTML = '<span class="inline-flex items-center gap-1 font-semibold text-emerald-700"><svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg> Sesuai syarat (' + len + ' karakter)</span>';
                                } else {
                                    var diff = minLength - len;
                                    counterStatusEl.innerHTML = '<span class="font-medium text-amber-700">Kurang ' + diff + ' karakter</span>';
                                }
                            }
                            textarea.value = data;
                        }

                        if (templateBtn) {
                            templateBtn.addEventListener('click', function () {
                                if (!activeEditor) return;
                                var templateHtml = [
                                    '<h3>1. Latar Belakang & Masalah</h3>',
                                    '<p>Jelaskan latar belakang pembuatan karya ini, siapa target penggunanya, dan masalah nyata yang ingin Anda selesaikan.</p>',
                                    '<h3>2. Solusi & Fitur Utama</h3>',
                                    '<p>Jabarkan fitur-fitur penting yang Anda buat. Anda dapat menyisipkan screenshot alur aplikasi atau video demo di bagian ini.</p>',
                                    '<h3>3. Peran & Kontribusi</h3>',
                                    '<p>Jelaskan tanggung jawab spesifik Anda (misalnya desain antarmuka, arsitektur basis data, backend API, atau pengujian).</p>',
                                    '<h3>4. Tantangan Teknis & Hasil</h3>',
                                    '<p>Ceritakan kendala teknis yang dihadapi selama implementasi, cara mengatasinya, dan hasil pengujian karya.</p>'
                                ].join('');

                                var current = activeEditor.getData();
                                var count = countPlainChars(current);
                                if (count <= 20 || confirm('Sisipkan kerangka struktur ke dalam deskripsi?')) {
                                    activeEditor.setData(count > 20 ? current + '<hr>' + templateHtml : templateHtml);
                                    updateCount();
                                }
                            });
                        }

                        ClassicEditor.create(textarea, {
                            toolbar: [
                                'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList',
                                'blockQuote', 'insertTable', 'imageUpload', 'mediaEmbed', 'undo', 'redo'
                            ],
                            image: {
                                toolbar: ['imageTextAlternative', 'toggleImageCaption', 'imageStyle:inline', 'imageStyle:block', 'imageStyle:side']
                            },
                            mediaEmbed: {
                                previewsInData: true
                            }
                        }).then(function (editor) {
                            activeEditor = editor;
                            editor.plugins.get('FileRepository').createUploadAdapter = function (loader) {
                                return new CkUploadAdapter(loader);
                            };

                            editor.model.document.on('change:data', updateCount);
                            updateCount();

                            var form = textarea.closest('form');
                            if (form) {
                                form.addEventListener('submit', function () {
                                    textarea.value = editor.getData();
                                });
                            }
                        }).catch(function (error) {
                            console.error(error);
                        });
                    });
                }

                if (window.ClassicEditor) {
                    start();
                    return;
                }

                var script = document.createElement('script');
                script.src = 'https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js';
                script.async = true;
                script.onload = start;
                document.head.appendChild(script);
            }

            function CkUploadAdapter(loader) {
                this.loader = loader;
            }
            CkUploadAdapter.prototype.upload = function () {
                return this.loader.file.then(function (file) {
                    var data = new FormData();
                    data.append('upload', file);
                    return fetch('{{ route('editor.upload') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: data
                    }).then(function (response) {
                        if (!response.ok) throw new Error('Upload gagal');
                        return response.json();
                    }).then(function (result) {
                        return { default: result.url };
                    });
                });
            };
            CkUploadAdapter.prototype.abort = function () {};

            function initImagePreviews() {
                document.querySelectorAll('[data-image-preview-input]').forEach(function (input) {
                    var image = document.querySelector(input.dataset.imagePreviewInput);
                    if (!image || input.dataset.ready) return;
                    input.dataset.ready = '1';
                    input.addEventListener('change', function () {
                        if (input.files && input.files[0]) image.src = URL.createObjectURL(input.files[0]);
                    });
                });
            }

            function initProjectTypes() {
                document.querySelectorAll('[data-project-type]').forEach(function (select) {
                    var fields = select.closest('form')?.querySelector('[data-team-fields]');
                    if (!fields) return;
                    function sync() { fields.classList.toggle('hidden', select.value !== 'team'); }
                    select.addEventListener('change', sync);
                    sync();
                });
            }

            function initContributorPickers() {
                document.querySelectorAll('[data-contributor-picker]').forEach(function (picker) {
                    if (picker.dataset.ready) return;
                    picker.dataset.ready = '1';

                    var search = picker.querySelector('[data-contributor-search]');
                    var options = Array.from(picker.querySelectorAll('[data-contributor-option]'));
                    var count = picker.querySelector('[data-contributor-count]');
                    var selectedWrap = picker.querySelector('[data-contributor-selected-wrap]');
                    var selectedList = picker.querySelector('[data-contributor-selected]');
                    var empty = picker.querySelector('[data-contributor-empty]');
                    var limitMessage = picker.querySelector('[data-contributor-limit]');
                    var max = Number(picker.dataset.max || 20);
                    var debounceTimer;
                    var controller;

                    function bindOption(option) {
                        option.querySelector('input[type="checkbox"]').addEventListener('change', render);
                    }

                    options.forEach(bindOption);

                    function selectedOptions() {
                        return options.filter(function (option) {
                            return option.querySelector('input[type="checkbox"]').checked;
                        });
                    }

                    function render() {
                        var selected = selectedOptions();
                        count.textContent = selected.length + ' / ' + max + ' dipilih';
                        selectedWrap.classList.toggle('hidden', selected.length === 0);
                        limitMessage.classList.toggle('hidden', selected.length < max);
                        selectedList.replaceChildren();

                        selected.forEach(function (option) {
                            var checkbox = option.querySelector('input[type="checkbox"]');
                            var name = option.querySelector('[data-contributor-name]').textContent.trim();
                            var chip = document.createElement('button');
                            chip.type = 'button';
                            chip.className = 'inline-flex max-w-full items-center gap-1.5 rounded-full bg-white px-3 py-1.5 text-xs font-semibold text-blue-800 ring-1 ring-blue-200 hover:bg-blue-100';
                            chip.setAttribute('aria-label', 'Hapus ' + name + ' dari anggota tim');
                            chip.textContent = name + ' ×';
                            chip.addEventListener('click', function () {
                                checkbox.checked = false;
                                render();
                            });
                            selectedList.appendChild(chip);
                        });

                        options.forEach(function (option) {
                            var checkbox = option.querySelector('input[type="checkbox"]');
                            checkbox.disabled = !checkbox.checked && selected.length >= max;
                            option.classList.toggle('opacity-50', checkbox.disabled);
                            option.classList.toggle('cursor-not-allowed', checkbox.disabled);
                            option.querySelector('[data-selected-label]').classList.toggle('hidden', !checkbox.checked);
                        });
                    }

                    function createOption(student) {
                        var existing = options.find(function (option) {
                            return option.querySelector('input').value === String(student.id);
                        });
                        if (existing) return existing;

                        var option = document.createElement('div');
                        option.className = 'relative flex items-center gap-3 border-b border-zinc-100 px-3 py-2.5 transition last:border-b-0 hover:bg-blue-50 has-[:checked]:bg-blue-50';
                        option.dataset.contributorOption = '';

                        var input = document.createElement('input');
                        input.id = 'contributor-' + student.id;
                        input.type = 'checkbox';
                        input.name = 'contributor_ids[]';
                        input.value = student.id;
                        input.className = 'h-4 w-4 shrink-0 rounded border-zinc-300 text-blue-600 focus:ring-blue-600';

                        var label = document.createElement('label');
                        label.htmlFor = input.id;
                        label.className = 'absolute inset-0 cursor-pointer';
                        label.setAttribute('aria-label', 'Pilih ' + student.name);

                        var avatar = document.createElement('span');
                        avatar.className = 'pointer-events-none flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-zinc-100 text-xs font-bold text-zinc-600';
                        avatar.textContent = student.name.charAt(0);

                        var details = document.createElement('span');
                        details.className = 'pointer-events-none min-w-0 flex-1';
                        var name = document.createElement('span');
                        name.className = 'block truncate text-sm font-medium text-zinc-900';
                        name.dataset.contributorName = '';
                        name.textContent = student.name;
                        var schoolClass = document.createElement('span');
                        schoolClass.className = 'block truncate text-xs text-zinc-500';
                        schoolClass.textContent = student.school_class || 'Kelas belum diatur';
                        details.append(name, schoolClass);

                        var selectedLabel = document.createElement('span');
                        selectedLabel.className = 'pointer-events-none hidden text-xs font-semibold text-blue-700';
                        selectedLabel.dataset.selectedLabel = '';
                        selectedLabel.textContent = 'Dipilih';

                        option.append(input, label, avatar, details, selectedLabel);
                        bindOption(option);
                        options.push(option);
                        return option;
                    }

                    function showResults(students, message) {
                        picker.querySelectorAll('[data-contributor-option]').forEach(function (option) {
                            option.classList.add('hidden');
                        });
                        students.forEach(function (student) {
                            var option = createOption(student);
                            option.classList.remove('hidden');
                            empty.before(option);
                        });
                        empty.textContent = message || 'Tidak ada siswa yang cocok.';
                        empty.classList.toggle('hidden', students.length !== 0);
                        render();
                    }

                    search.addEventListener('input', function () {
                        clearTimeout(debounceTimer);
                        var query = search.value.trim();
                        if (query.length < 2) {
                            if (controller) controller.abort();
                            showResults([], 'Ketik minimal 2 karakter untuk mencari siswa.');
                            return;
                        }

                        empty.textContent = 'Mencari siswa...';
                        empty.classList.remove('hidden');
                        debounceTimer = setTimeout(function () {
                            if (controller) controller.abort();
                            controller = new AbortController();
                            fetch(picker.dataset.searchUrl + '?q=' + encodeURIComponent(query), {
                                headers: { 'Accept': 'application/json' },
                                signal: controller.signal
                            }).then(function (response) {
                                if (!response.ok) throw new Error('Pencarian siswa gagal');
                                return response.json();
                            }).then(function (students) {
                                showResults(students);
                            }).catch(function (error) {
                                if (error.name !== 'AbortError') showResults([], 'Pencarian gagal. Coba lagi.');
                            });
                        }, 250);
                    });
                    render();
                });
            }

            function initShareButtons() {
                document.querySelectorAll('[data-share-url]').forEach(function (button) {
                    button.addEventListener('click', async function () {
                        var original = button.textContent;
                        var data = { title: button.dataset.shareTitle, text: button.dataset.shareText, url: button.dataset.shareUrl };
                        try {
                            if (navigator.share) await navigator.share(data);
                            else {
                                await navigator.clipboard.writeText(data.url);
                                button.textContent = 'Link tersalin';
                                setTimeout(function () { button.textContent = original; }, 1800);
                            }
                        } catch (error) {
                            if (error.name !== 'AbortError') console.error(error);
                        }
                    });
                });
            }
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', boot);
            } else {
                boot();
            }
        })();
    </script>

    <style>
        html { font-family: 'Archivo', system-ui, sans-serif; }
        .font-display { font-family: 'Space Grotesk', system-ui, sans-serif; letter-spacing: -0.02em; }
        .text-balance { text-wrap: balance; }
        .grain { background-image: radial-gradient(rgb(0 0 0 / 0.025) 1px, transparent 1px); background-size: 4px 4px; }
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation-duration: 0.01ms !important; transition-duration: 0.01ms !important; }
        }
        .scrollbar-thin::-webkit-scrollbar { width: 6px; height: 6px; }
        .scrollbar-thin::-webkit-scrollbar-thumb { background: #d4d4d8; border-radius: 3px; }
        .sidebar-transition { transition: transform 200ms ease-out; }
        .backdrop-fade { transition: opacity 200ms ease-out; }
        .rich-content img { max-width: 100%; border-radius: 0.75rem; margin: 0.75rem 0; }
        .rich-content a { color: #2563eb; text-decoration: underline; }
        .rich-content ul, .rich-content ol { margin: 0.75rem 0 0.75rem 1.25rem; }
        .rich-content ul { list-style: disc; }
        .rich-content ol { list-style: decimal; }
        .rich-content blockquote { border-left: 3px solid #d4d4d8; padding-left: 0.75rem; color: #52525b; }
        .rich-content figure { margin: 1rem 0; }
        .rich-content iframe { aspect-ratio: 16 / 9; width: 100%; border: 0; border-radius: 0.75rem; }
        .rich-content table { width: 100%; border-collapse: collapse; margin: 1rem 0; font-size: 0.875rem; }
        .rich-content th, .rich-content td { border: 1px solid #e4e4e7; padding: 0.5rem; text-align: left; }
        .ck-editor__editable { min-height: 14rem; }
        .ck-content img { max-width: 100%; }
    </style>
    @stack('head')
</head>
<body class="h-full text-zinc-800">
    <a href="#main" class="sr-only focus:not-sr-only focus:fixed focus:left-3 focus:top-3 focus:z-50 focus:rounded focus:bg-zinc-900 focus:px-3 focus:py-2 focus:text-white focus:text-sm">Lewati ke konten</a>
    @yield('body')
</body>
</html>
