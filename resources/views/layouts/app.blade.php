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
        }

        body { color: var(--fs-text); }

        /* Make common utility colors slightly darker for better contrast */
        .bg-blue-600 { background-color: var(--fs-primary) !important; }
        .bg-blue-700 { background-color: #15326b !important; }
        .bg-green-600 { background-color: var(--fs-green) !important; }

        /* Links default to primary color, but keep hover underline for affordance */
        a:not(.text-white) { color: var(--fs-primary); }
        /* Ensure anchors explicitly marked as text-white are rendered white (higher specificity) */
        a.text-white { color: #ffffff !important; }
        a:hover { text-decoration: underline; }

        /* Ensure buttons with pale backgrounds have dark text */
        .bg-gray-100, .bg-gray-200, .bg-blue-50 { color: var(--fs-text) !important; }

        /* Improve footer contrast */
        footer { color: #d1d5db; }
    </style>
</head>
<body class="bg-gray-100 flex flex-col min-h-screen">

<nav class="bg-blue-800 text-white shadow-md">
    <div class="container mx-auto px-4 py-4 flex justify-between items-center">
        <a href="/" class="text-2xl font-bold tracking-wider text-white">👕 Low Cortisol</a>
        <div class="flex items-center space-x-6">
            <a href="{{ route('catalog.index') }}" class="hover:underline text-white">Catálogo</a>
            <a href="{{ route('cart.index') }}" class="hover:underline text-white">Carrinho</a>

            @auth
                <div class="relative">
                    <button id="profileToggle" class="text-sm bg-blue-700 text-white px-3 py-1 rounded focus:outline-none">Olá, {{ Auth::user()->name }}</button>
                    <div id="profileDropdown" class="absolute right-0 mt-2 hidden">
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

    <footer class="bg-gray-900 text-gray-300 py-6 text-center text-sm mt-12">
        &copy; {{ date('Y') }} FunShirt - Projecto de Aplicações para a Internet.
    </footer>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var btn = document.getElementById('profileToggle');
        var dropdown = document.getElementById('profileDropdown');
        if (btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                dropdown.classList.toggle('hidden');
            });
            document.addEventListener('click', function(e) {
                if (!btn.contains(e.target) && !dropdown.contains(e.target)) {
                    dropdown.classList.add('hidden');
                }
            });
        }
    });
</script>

</body>
</html>
