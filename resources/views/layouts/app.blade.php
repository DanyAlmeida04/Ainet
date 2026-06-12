<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FunShirt - Loja Online</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style>
        :root{
            --fs-primary: #1e3a8a;
            --fs-primary-contrast: #ffffff;
            --fs-green: #15803d;
            --fs-text: #111827;
            --fs-muted: #6b7280;
            --card-bg: #ffffff;
            --page-bg: #f3f4f6;
            --nav-bg: #1e40af;
            --footer-bg: #111827;
        }

        .dark {
            --fs-primary: #60a5fa;
            --fs-primary-contrast: #0b1220;
            --fs-green: #34d399;
            --fs-text: #e6eef8;
            --fs-muted: #9ca3af;
            --card-bg: #0b1220;
            --page-bg: #071127;
            --nav-bg: #041024;
            --footer-bg: #021018;
        }

        body { color: var(--fs-text); background-color: var(--page-bg); }
        nav { background-color: var(--nav-bg); }
        .bg-blue-600 { background-color: var(--fs-primary) !important; }
        .bg-blue-700 { background-color: #15326b !important; }
        .bg-green-600 { background-color: var(--fs-green) !important; }
        a:not(.text-white) { color: var(--fs-primary); }
        a:hover { text-decoration: underline; }
        .bg-gray-100, .bg-gray-200, .bg-blue-50 { color: var(--fs-text) !important; }
        .card-bg { background-color: var(--card-bg); }
        footer { color: var(--fs-muted); background-color: var(--footer-bg); }
        .profile-dropdown { color: var(--fs-text); }

        .dark .bg-white {
            background-color: var(--card-bg) !important;
        }

        .dark .bg-blue-50 {
            background-color: #0f172a !important;
            color: var(--fs-text) !important;
        }

        .dark .text-gray-500,
        .dark .text-gray-600,
        .dark .text-gray-700,
        .dark .text-gray-800,
        .dark .text-gray-900,
        .dark .text-black {
            color: var(--fs-text) !important;
        }

        .dark .text-blue-600 {
            color: #93c5fd !important;
        }

        .dark .text-blue-800 {
            color: #e2e8f0 !important;
        }

        .dark .border,
        .dark .border-gray-100,
        .dark .border-gray-200,
        .dark .border-gray-300 {
            border-color: #334155 !important;
        }

        .dark table {
            background-color: var(--card-bg) !important;
            color: var(--fs-text) !important;
        }

        .dark thead {
            background-color: #0f172a !important;
        }

        .dark tbody tr {
            border-color: #334155 !important;
        }

        .dark tbody tr:nth-child(even) {
            background-color: rgba(255, 255, 255, 0.03) !important;
        }

        .dark input,
        .dark select,
        .dark textarea {
            background-color: #0f172a !important;
            color: var(--fs-text) !important;
            border-color: #334155 !important;
        }

        .dark ::placeholder {
            color: #94a3b8 !important;
        }

        .dark .text-yellow-800 {
            color: #fde68a !important;
        }

        .dark .bg-yellow-100 {
            background-color: #422006 !important;
        }

        :root {
            --pagination-bg: #ffffff;
            --pagination-text: #334155;
            --pagination-border: #e2e8f0;
            --pagination-item-bg: #ffffff;
            --pagination-item-hover: #f8fafc;
            --pagination-muted: #94a3b8;
            --pagination-current-bg: #dbeafe;
            --pagination-current-text: #1d4ed8;
            --pagination-current-border: #bfdbfe;
        }

        .dark {
            --pagination-bg: #0f172a;
            --pagination-text: #e2e8f0;
            --pagination-border: #334155;
            --pagination-item-bg: #0f172a;
            --pagination-item-hover: #111827;
            --pagination-muted: #94a3b8;
            --pagination-current-bg: #2563eb;
            --pagination-current-text: #ffffff;
            --pagination-current-border: #2563eb;
        }

        .pagination-clean nav[role="navigation"] {
            background: var(--pagination-bg) !important;
            color: var(--pagination-text) !important;
            border: 1px solid var(--pagination-border) !important;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.06);
        }

        .pagination-clean nav[role="navigation"] a,
        .pagination-clean nav[role="navigation"] span,
        .pagination-clean nav[role="navigation"] button {
            background: var(--pagination-item-bg) !important;
            color: var(--pagination-text) !important;
            border-color: var(--pagination-border) !important;
        }

        .pagination-clean nav[role="navigation"] a:hover {
            background: var(--pagination-item-hover) !important;
        }

        .pagination-clean nav[role="navigation"] .pagination-muted {
            color: var(--pagination-muted) !important;
        }

        .pagination-clean nav[role="navigation"] [aria-current="page"] {
            background: var(--pagination-current-bg) !important;
            color: var(--pagination-current-text) !important;
            border-color: var(--pagination-current-border) !important;
        }

    </style>
</head>
<body class="bg-gray-100 flex flex-col min-h-screen" id="pageRoot">

<nav class="bg-blue-800 text-white shadow-md" id="mainNav">
    <div class="container mx-auto px-4 py-4 flex justify-between items-center">
        <a href="/" class="text-2xl font-bold tracking-wider text-white">👕 Low Cortisol</a>
        <div class="flex items-center space-x-4">
            <a href="{{ route('catalog.index') }}" class="hover:underline text-white">Catálogo</a>
            <a href="{{ route('cart.index') }}" class="hover:underline text-white">Carrinho</a>

            <button id="themeToggle" title="Alternar tema claro/escuro" class="inline-flex items-center gap-2 px-3 py-1 rounded-lg bg-gray-200 text-sm font-medium transition-colors duration-200 hover:bg-gray-300" aria-pressed="false">
                <span id="themeToggleIcon" aria-hidden="true">🌙</span>
                <span id="themeToggleLabel">Dark mode</span>
            </button>

            @auth
                @php $currentUser = Auth::user(); @endphp
                @if($currentUser && $currentUser->user_type === 'A' && !($currentUser->blocked ?? false))
                    <a href="{{ route('admin.dashboard') }}" class="bg-white text-blue-800 px-3 py-1 rounded font-semibold hover:bg-gray-100">Admin</a>
                @endif

                <div class="relative">
                    <button id="profileToggle" class="text-sm bg-blue-700 text-white px-3 py-1 rounded focus:outline-none">Olá, {{ Auth::user()->name }}</button>
                    <div id="profileDropdown" class="absolute right-0 mt-2 hidden profile-dropdown card-bg rounded shadow p-2">
                        @include('auth.quick_profile')
                    </div>
                </div>

                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="hover:underline text-red-200">Sair</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="hover:underline text-white">Entrar</a>
                <a href="{{ route('register') }}" class="bg-white text-blue-800 px-3 py-1 rounded font-semibold hover:bg-gray-100">Registar</a>
            @endauth
        </div>
    </div>
</nav>

    @if(session('success'))
        <div id="flashToast" class="fixed bottom-4 right-4 z-50 w-[calc(100vw-2rem)] max-w-sm transition-all duration-300 ease-out">
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 shadow-lg dark:border-emerald-900/40 dark:bg-emerald-950/70 dark:text-emerald-200">
                {{ session('success') }}
            </div>
        </div>
    @endif

    <main class="grow">
        @yield('content')
    </main>

    <footer class="py-6 text-center text-sm mt-12">
        &copy; {{ date('Y') }} FunShirt - Projecto de Aplicações para a Internet.
    </footer>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var profileToggle = document.getElementById('profileToggle');
        var profileDropdown = document.getElementById('profileDropdown');

        if (profileToggle && profileDropdown) {
            profileToggle.addEventListener('click', function (event) {
                event.preventDefault();
                profileDropdown.classList.toggle('hidden');
            });

            document.addEventListener('click', function (event) {
                if (!profileToggle.contains(event.target) && !profileDropdown.contains(event.target)) {
                    profileDropdown.classList.add('hidden');
                }
            });
        }

        var themeToggle = document.getElementById('themeToggle');
        var themeToggleIcon = document.getElementById('themeToggleIcon');
        var themeToggleLabel = document.getElementById('themeToggleLabel');
        if (!themeToggle) {
            return;
        }

        var root = document.documentElement;
        var storedTheme = localStorage.getItem('fs-theme');
        var prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
        var initialTheme = storedTheme || (prefersDark ? 'dark' : 'light');

        function updateThemeButton(isDark) {
            if (themeToggleIcon) {
                themeToggleIcon.textContent = isDark ? '☀️' : '🌙';
            }
            if (themeToggleLabel) {
                themeToggleLabel.textContent = isDark ? 'Light mode' : 'Dark mode';
            }
            themeToggle.title = isDark ? 'Alternar para light mode' : 'Alternar para dark mode';
            themeToggle.setAttribute('aria-pressed', isDark ? 'true' : 'false');
            themeToggle.classList.toggle('bg-gray-200', !isDark);
            themeToggle.classList.toggle('bg-slate-800', isDark);
            themeToggle.classList.toggle('text-slate-100', isDark);
            themeToggle.classList.toggle('hover:bg-gray-300', !isDark);
            themeToggle.classList.toggle('hover:bg-slate-700', isDark);
        }

        function applyTheme(theme) {
            var isDark = theme === 'dark';
            root.classList.toggle('dark', isDark);
            localStorage.setItem('fs-theme', isDark ? 'dark' : 'light');
            updateThemeButton(isDark);
        }

        applyTheme(initialTheme);

        themeToggle.addEventListener('click', function () {
            applyTheme(root.classList.contains('dark') ? 'light' : 'dark');
        });

        var flashToast = document.getElementById('flashToast');
        if (flashToast) {
            window.setTimeout(function () {
                flashToast.style.opacity = '0';
                flashToast.style.transform = 'translateY(8px)';
                window.setTimeout(function () {
                    flashToast.remove();
                }, 300);
            }, 5000);
        }
    });
</script>

@stack('scripts')

</body>
</html>
