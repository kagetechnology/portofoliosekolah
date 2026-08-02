<!DOCTYPE html>
<html lang="id" class="h-full bg-zinc-50 antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#18181b">
    <title>@yield('title', 'Portofolio Sekolah')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Archivo:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@400;500;600;700&display=swap">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Archivo', 'system-ui', 'sans-serif'],
                        display: ['"Space Grotesk"', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        ink: { DEFAULT: '#09090b', soft: '#18181b', line: '#e4e4e7' },
                        brand: { 50: '#eff6ff', 100: '#dbeafe', 500: '#3b82f6', 600: '#2563eb', 700: '#1d4ed8' },
                    },
                    boxShadow: {
                        soft: '0 1px 2px rgb(0 0 0 / 0.04), 0 8px 24px -12px rgb(24 24 27 / 0.12)',
                        ring: '0 0 0 4px rgb(24 24 27 / 0.06)',
                    }
                }
            }
        };
    </script>
    <script src="https://cdn.tailwindcss.com"></script>

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
                    nav.classList.toggle('shadow-soft', window.scrollY > 8);
                    nav.classList.toggle('border-zinc-200/80', window.scrollY > 8);
                }, { passive: true });
            }

            function boot() {
                document.querySelectorAll('[data-sidebar-root]').forEach(initSidebar);
                initPublicNav();
                initRichEditors();
                initImagePreviews();
            }

            function initRichEditors() {
                document.querySelectorAll('[data-rich-editor]').forEach(function (root) {
                    var content = root.querySelector('[data-rich-content]');
                    var textarea = root.querySelector('textarea');
                    if (!content || !textarea || root.dataset.ready) return;
                    root.dataset.ready = '1';

                    function sync() { textarea.value = content.innerHTML.trim(); }
                    root.querySelectorAll('[data-rich-command]').forEach(function (button) {
                        button.addEventListener('click', function () {
                            content.focus();
                            document.execCommand(button.dataset.richCommand, false, button.dataset.richValue || null);
                            sync();
                        });
                    });
                    var link = root.querySelector('[data-rich-link]');
                    if (link) link.addEventListener('click', function () {
                        var url = prompt('URL link (https://...)');
                        if (!url) return;
                        content.focus();
                        document.execCommand('createLink', false, url);
                        sync();
                    });
                    var image = root.querySelector('[data-rich-image]');
                    if (image) image.addEventListener('click', function () {
                        var url = prompt('URL gambar (https://...)');
                        if (!url) return;
                        content.focus();
                        document.execCommand('insertImage', false, url);
                        sync();
                    });
                    content.addEventListener('input', sync);
                    if (content.closest('form')) content.closest('form').addEventListener('submit', sync);
                });
            }

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
    </style>
    @stack('head')
</head>
<body class="h-full text-zinc-800">
    <a href="#main" class="sr-only focus:not-sr-only focus:fixed focus:left-3 focus:top-3 focus:z-50 focus:rounded focus:bg-zinc-900 focus:px-3 focus:py-2 focus:text-white focus:text-sm">Lewati ke konten</a>
    @yield('body')
</body>
</html>
