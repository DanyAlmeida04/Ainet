@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">

    {{-- Admin Panel Persistent Navigation --}}
    <div class="mb-8 flex flex-col items-center">
        <div class="w-full flex items-center justify-between mb-4 border-b border-slate-200/60 dark:border-slate-800 pb-3">
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white">
                Painel de Administração
            </h1>
            <a href="{{ route('catalog.index') }}" class="text-sm font-medium text-slate-500 hover:text-blue-600 hover:underline dark:text-slate-400 dark:hover:text-blue-400 transition-colors">
                &larr; Voltar à Loja
            </a>
        </div>

        <nav class="flex flex-wrap justify-center gap-1.5 bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200/60 dark:border-slate-800 p-2 w-full md:w-auto">
            @php
                $currentRoute = Route::currentRouteName();
                $navItems = [
                    ['route' => 'admin.dashboard',         'label' => 'Estatísticas',   'match' => 'admin.dashboard'],
                    ['route' => 'admin.orders.index',      'label' => 'Encomendas',      'match' => 'admin.orders'],
                    ['route' => 'admin.users.index',       'label' => 'Utilizadores',    'match' => 'admin.users'],
                    ['route' => 'admin.categories.index',  'label' => 'Categorias',      'match' => 'admin.categories'],
                    ['route' => 'admin.designs.index',     'label' => 'Designs',         'match' => 'admin.designs'],
                    ['route' => 'admin.reports.index',     'label' => 'Reports',         'match' => 'admin.reports'],
                    ['route' => 'admin.prices.edit',       'label' => 'Preços',          'match' => 'admin.prices'],
                    ['route' => 'admin.colors.index',      'label' => 'Cores',           'match' => 'admin.colors'],
                ];
            @endphp

            @foreach($navItems as $item)
                @php
                    $isActive = str_starts_with($currentRoute ?? '', $item['match']);
                @endphp
                <a href="{{ route($item['route']) }}"
                   class="flex items-center gap-1.5 px-5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-150
                          {{ $isActive
                             ? 'bg-blue-600 text-white shadow-sm'
                             : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>
    </div>

    {{-- Page Content --}}
    @yield('admin-content')

</div>
@endsection
