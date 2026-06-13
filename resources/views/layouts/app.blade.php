<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FunShirt - Loja Online</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style>
        @variant dark (&:where(.dark, .dark *));

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
        
        /* Default links: color them only if they do NOT have an explicit Tailwind text- color class */
        a:not([class*="text-"]) { color: var(--fs-primary); }
        a:hover { text-decoration: underline; }
        
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

        /* Map common slate/gray background utilities to darker equivalents in dark mode */
        .dark .bg-gray-50, .dark .bg-slate-50 { background-color: #0b1329 !important; }
        .dark .bg-gray-100, .dark .bg-slate-100 { background-color: #0f172a !important; }
        .dark .bg-gray-200, .dark .bg-slate-200 { background-color: #1e293b !important; }
        .dark .bg-gray-305, .dark .bg-gray-300, .dark .bg-slate-300 { background-color: #334155 !important; }

        /* Retain typographic hierarchy by mapping grays to corresponding slate/gray shades in dark mode */
        .dark .text-black, .dark .text-gray-900, .dark .text-slate-900 { color: #f8fafc !important; }
        .dark .text-gray-800, .dark .text-slate-800 { color: #f1f5f9 !important; }
        .dark .text-gray-700, .dark .text-slate-700 { color: #e2e8f0 !important; }
        .dark .text-gray-600, .dark .text-slate-600 { color: #cbd5e1 !important; }
        .dark .text-gray-500, .dark .text-slate-500 { color: #94a3b8 !important; }
        .dark .text-gray-400, .dark .text-slate-400 { color: #64748b !important; }

        .dark .text-blue-600 {
            color: #93c5fd !important;
        }

        .dark .text-blue-800 {
            color: #e2e8f0 !important;
        }

        /* Map border colors dynamically */
        .dark .border,
        .dark [class*="border-gray-"],
        .dark [class*="border-slate-"] {
            border-color: #1e293b !important;
        }

        .dark table {
            background-color: var(--card-bg) !important;
            color: var(--fs-text) !important;
        }

        .dark thead {
            background-color: #0f172a !important;
        }

        .dark tbody tr {
            border-color: #1e293b !important;
        }

        .dark tbody tr:nth-child(even) {
            background-color: rgba(255, 255, 255, 0.02) !important;
        }

        .dark input,
        .dark select,
        .dark textarea {
            background-color: #0f172a !important;
            color: var(--fs-text) !important;
            border-color: #1e293b !important;
        }

        .dark ::placeholder {
            color: #475569 !important;
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
        <a href="/" class="text-2xl font-bold tracking-wider text-white">FunShirt</a>
        <div class="flex items-center space-x-4">
            <a href="{{ route('catalog.index') }}" class="hover:underline text-white">Catálogo</a>
            @if(!auth()->check() || auth()->user()->user_type === 'C')
                <a href="{{ route('profile.images.index') }}" class="hover:underline text-white">Personalizar T-shirt</a>
            @endif
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

@auth
    @php
        $pendingNotification = \App\Models\ReceiptReport::where('user_id', Auth::id())
            ->where('notified', false)
            ->whereIn('status', ['accepted', 'rejected'])
            ->first();
    @endphp

    @if($pendingNotification)
        <div id="receiptReportNotificationModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/75 backdrop-blur-sm">
            <div class="bg-white dark:bg-slate-900 w-full max-w-md rounded-2xl shadow-xl border border-slate-200/60 dark:border-slate-800 p-6 text-center m-4 animate-fade-in">
                
                @if($pendingNotification->status === 'accepted')
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-emerald-100 dark:bg-emerald-950/30 text-emerald-600 dark:text-emerald-400 mb-4">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-2">Recibo Regenerado!</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-6 leading-relaxed">
                        O seu recibo da <strong>Encomenda #{{ $pendingNotification->order_id }}</strong> foi regenerado e reenviado para o seu e-mail.
                    </p>

                    <div class="flex flex-col gap-2">
                        <a href="{{ route('orders.preview', $pendingNotification->order_id) }}" 
                           target="_blank" 
                           onclick="document.getElementById('dismissReportNotificationForm').submit()"
                           class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2.5 px-4 rounded-xl text-sm transition text-center shadow-sm">
                            Visualizar Recibo
                        </a>
                        <form id="dismissReportNotificationForm" action="{{ route('orders.dismissNotification', $pendingNotification) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold py-2.5 px-4 rounded-xl text-sm transition cursor-pointer">
                                Fechar
                            </button>
                        </form>
                    </div>
                @else
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-rose-100 dark:bg-rose-950/30 text-rose-600 dark:text-rose-400 mb-4">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-2">Pedido Recusado</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-6 leading-relaxed">
                        O seu pedido de reenvio/regeneração de recibo para a <strong>Encomenda #{{ $pendingNotification->order_id }}</strong> foi analisado e recusado pela administração.
                    </p>

                    <form action="{{ route('orders.dismissNotification', $pendingNotification) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 px-4 rounded-xl text-sm transition shadow-sm cursor-pointer">
                            Entendido
                        </button>
                    </form>
                @endif
                
            </div>
        </div>
    @endif
@endauth

</body>
</html>
