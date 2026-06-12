<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FunShirt - Loja Online</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style>
        /* Improve contrast and fix white-on-white issues */
        :root{
            --fs-primary: #1e3a8a; /* darker blue */
            --fs-primary-contrast: #ffffff;
            --fs-green: #15803d; /* darker green */
            --fs-text: #111827; /* dark text */
            --fs-muted: #6b7280;
            --card-bg: #ffffff;
            --page-bg: #f3f4f6;
            --nav-bg: #1e40af;
            --footer-bg: #111827;
        }

        /* Dark mode variables under .dark on root */
        .dark {
            --fs-primary: #60a5fa; /* lighter blue for dark bg */
            --fs-primary-contrast: #0b1220;
            --fs-green: #34d399;
            --fs-text: #e6eef8; /* light text */
            --fs-muted: #9ca3af;
            --card-bg: #0b1220;
            --page-bg: #071127;
            --nav-bg: #041024;
            --footer-bg: #021018;
        }

        body { color: var(--fs-text); background-color: var(--page-bg); }

        /* Make common utility colors slightly darker for better contrast */
        .bg-blue-600 { background-color: var(--fs-primary) !important; }
        .bg-blue-700 { background-color: #15326b !important; }
        .bg-green-600 { background-color: var(--fs-green) !important; }

        /* Links default to primary color, but keep hover underline for affordance */
        a:not(.text-white) { color: var(--fs-primary); }
        /* Ensure anchors explicitly marked as text-white are rendered white (higher specificity) */
        a.text-white { color: var(--fs-primary-contrast) !important; }
        a:hover { text-decoration: underline; }

        /* Ensure buttons with pale backgrounds have dark text */
        .bg-gray-100, .bg-gray-200, .bg-blue-50 { color: var(--fs-text) !important; }

        /* Ensure cards use card background variable */
        .card-bg { background-color: var(--card-bg); }

        /* Improve footer contrast */
        footer { color: var(--fs-muted); background-color: var(--footer-bg); }

        /* Profile dropdown explicit text color to avoid inheritance problems */
        .profile-dropdown { color: var(--fs-text); }

    </style>
</head>
<body class="bg-gray-100 flex flex-col min-h-screen" id="pageRoot">

<nav class="bg-blue-800 text-white shadow-md" id="mainNav" style="background-color:var(--nav-bg)">
    <div class="container mx-auto px-4 py-4 flex justify-between items-center">
        <a href="/" class="text-2xl font-bold tracking-wider text-white">👕 Low Cortisol</a>
        <div class="flex items-center space-x-4">
            <a href="{{ route('catalog.index') }}" class="hover:underline text-white">Catálogo</a>
            <a href="{{ route('cart.index') }}" class="hover:underline text-white">Carrinho</a>

            @auth
                @can('manage-users')
                    <a href="{{ route('admin.dashboard') }}" class="bg-white text-blue-800 px-3 py-1 rounded font-semibold hover:bg-gray-100">Admin</a>
                @endcan

                <button id="themeToggle" title="Alternar tema claro/escuro" class="px-3 py-1 rounded bg-gray-200 text-sm">Modo</button>

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

    <main class="flex-grow">
        @yield('content')
    </main>

    <footer class="py-6 text-center text-sm mt-12">
        &copy; {{ date('Y') }} FunShirt - Projecto de Aplicações para a Internet.
    </footer>

<script>
    // Profile dropdown toggle
    document.addEventListener('DOMContentLoaded', function() {
        var btn = document.getElementById('profileToggle');
        var dropdown = document.getElementById('profileDropdown');
        if (btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                dropdown.classList.toggle('hidden');
            });
            document.addEventListener('click', function(e) {
                if (!btn.contains(e.target) && dropdown && !dropdown.contains(e.target)) {
                    dropdown.classList.add('hidden');
                }
            });
        }

        // Theme toggle
        var themeToggle = document.getElementById('themeToggle');
        var root = document.documentElement || document.getElementById('pageRoot');
        var stored = localStorage.getItem('fs-theme');
        if (stored === 'dark') {
            document.documentElement.classList.add('dark');
        }
        if (themeToggle) {
            themeToggle.addEventListener('click', function() {
                document.documentElement.classList.toggle('dark');
                var isDark = document.documentElement.classList.contains('dark');
                localStorage.setItem('fs-theme', isDark ? 'dark' : 'light');
            });
        }
    });
</script>

</body>
</html>
