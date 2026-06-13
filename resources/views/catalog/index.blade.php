@extends('layouts.app') {{-- Assumindo que tens um layout base chamado app --}}

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold">Catálogo de T-Shirts</h1>
        @can('manage-users')
            <a href="{{ route('admin.dashboard') }}" class="ml-4 px-4 py-2 bg-white text-blue-800 rounded shadow hover:bg-gray-100">Admin</a>
        @endcan
    </div>

    @auth
        @if(Auth::user()->user_type === 'C')
            <div class="mb-6 p-4 md:p-6 bg-gradient-to-r from-blue-600 to-indigo-700 text-white rounded-2xl shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
                <div>
                    <h3 class="text-lg font-bold">Queres usar a tua própria imagem?</h3>
                    <p class="text-blue-100 text-xs md:text-sm mt-1">Carrega o teu design personalizado e cria uma t-shirt exclusiva com estampagem à tua escolha!</p>
                </div>
                <a href="{{ route('profile.images.index') }}" class="px-5 py-2.5 bg-white text-blue-700 hover:bg-blue-50 text-xs md:text-sm font-bold rounded-xl shadow transition shrink-0 hover:no-underline">
                    🎨 Carregar Imagem
                </a>
            </div>
        @endif
    @else
        <div class="mb-6 p-4 md:p-6 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl flex flex-col md:flex-row items-center justify-between gap-4">
            <div>
                <h3 class="text-lg font-bold text-slate-800 dark:text-white">Queres usar a tua própria imagem?</h3>
                <p class="text-slate-500 dark:text-slate-400 text-xs md:text-sm mt-1">Faz login ou regista-te para carregar os teus designs personalizados e estampar t-shirts exclusivas.</p>
            </div>
            <div class="flex gap-2 shrink-0">
                <a href="{{ route('login') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs md:text-sm font-bold rounded-xl shadow transition hover:no-underline">Entrar</a>
                <a href="{{ route('register') }}" class="px-4 py-2 bg-white text-slate-700 border border-slate-200 hover:bg-slate-50 text-xs md:text-sm font-bold rounded-xl transition hover:no-underline">Registar</a>
            </div>
        </div>
    @endauth

    <div class="flex gap-6">
        {{-- Sidebar: lista de categorias --}}
        <aside class="w-64 bg-white rounded shadow p-4">
            <h2 class="font-semibold mb-3">Categorias</h2>
            <ul class="space-y-2">
                <li>
                    <a href="{{ route('catalog.index') }}" class="block px-3 py-2 rounded transition {{ request('category') ? 'hover:bg-gray-100' : 'bg-blue-50 font-semibold' }}">Todas as categorias</a>
                </li>
                @foreach($categories as $cat)
                    <li>
                        <a href="{{ route('catalog.index', array_merge(request()->query(), ['category' => $cat->id])) }}"
                           class="flex items-center gap-3 px-3 py-2 rounded transition hover:bg-gray-100 {{ request('category') == $cat->id ? 'bg-blue-50 font-semibold' : '' }}">
                            {{-- Thumbnail: category image or first tshirt image -> fallback --}}
                            @php
                                $thumb = $cat->image_url ? asset('storage/categories/' . $cat->image_url) : null;
                                if (! $thumb) {
                                    $first = $cat->tshirtImages()->select('image_url')->first();
                                    $thumb = $first ? asset('storage/tshirt_images/' . $first->image_url) : asset('storage/categories/default_category.png');
                                }
                            @endphp
                            <img src="{{ $thumb }}" alt="{{ $cat->name }}" class="w-12 h-12 object-cover rounded">
                            <div class="flex-1 text-sm">{{ $cat->name }}</div>
                        </a>
                    </li>
                @endforeach
            </ul>
        </aside>

        {{-- Main: pesquisa + grelha de produtos --}}
        <main class="flex-1">
            <form action="{{ route('catalog.index') }}" method="GET" class="mb-4 flex gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Pesquisar por nome ou descrição..." class="border rounded px-4 py-2 flex-1">
                @if(request('category'))
                    <input type="hidden" name="category" value="{{ request('category') }}">
                @endif
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded shadow-sm cursor-pointer transition hover:underline hover:bg-blue-700">Pesquisar</button>
            </form>

            <div class="mb-4 flex items-center justify-between">
                <div class="text-lg font-semibold">{{ request('category') ? ($categories->firstWhere('id', request('category'))->name ?? 'Categoria') : 'Todas as T-Shirts' }}</div>
                @if(request('category'))
                    <a href="{{ route('catalog.index') }}" class="text-sm text-blue-600 hover:underline">Ver todas as categorias</a>
                @endif
            </div>

            @if($tshirtImages->count() == 0)
                <div class="bg-yellow-100 text-yellow-800 p-4 rounded">Nenhuma t-shirt encontrada.</div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach($tshirtImages as $image)
                        <div class="border rounded-lg p-4 shadow hover:shadow-lg transition flex flex-col justify-between bg-white">
                            <div>
                                <a href="{{ route('catalog.show', $image->id) }}" class="block mb-4">
                                    <img src="{{ asset('storage/tshirt_images/' . $image->image_url) }}" alt="{{ $image->name }}" class="w-full h-48 object-contain rounded hover:opacity-90 transition">
                                </a>
                                <h2 class="font-bold text-lg mb-1">
                                    <a href="{{ route('catalog.show', $image->id) }}" class="hover:underline text-inherit">{{ $image->name }}</a>
                                </h2>
                                <p class="text-gray-600 text-sm mb-4">{{ Str::limit($image->description, 80) }}</p>
                            </div>

                            <div class="flex items-center justify-between mt-auto">
                                <span class="text-xs bg-slate-100 text-slate-700 px-2 py-1 rounded dark:bg-slate-700 dark:text-slate-200">{{ $image->category->name ?? 'Sem Categoria' }}</span>
                                <div>
                                    <a href="{{ route('catalog.show', $image->id) }}" class="inline-block bg-blue-600 text-white px-3 py-1.5 rounded text-sm font-semibold transition hover:bg-blue-700 hover:no-underline">Ver detalhes</a>
                                </div>
                            </div>
                        </div>
                    @endforeach

                </div>

                @if($tshirtImages->hasPages())
                    <nav class="mt-8 flex flex-col gap-3 rounded-xl border border-slate-200 bg-white px-4 py-3 shadow-sm text-slate-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 sm:flex-row sm:items-center sm:justify-between">
                        <div class="text-sm text-slate-600 dark:text-slate-300">
                            Showing {{ $tshirtImages->firstItem() }} to {{ $tshirtImages->lastItem() }} of {{ $tshirtImages->total() }} results
                        </div>

                        <div class="flex flex-wrap items-center gap-1">
                            @if($tshirtImages->onFirstPage())
                                <span class="pagination-muted inline-flex h-10 min-w-10 items-center justify-center rounded-lg border border-slate-200 bg-slate-100 px-3 dark:border-slate-700 dark:bg-slate-800">&lsaquo;</span>
                            @else
                                <a href="{{ $tshirtImages->previousPageUrl() }}" class="inline-flex h-10 min-w-10 items-center justify-center rounded-lg border border-slate-200 bg-white px-3 text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">&lsaquo;</a>
                            @endif

                            @php
                                $currentPage = $tshirtImages->currentPage();
                                $lastPage = $tshirtImages->lastPage();
                                $startPage = max(1, $currentPage - 2);
                                $endPage = min($lastPage, $currentPage + 2);
                            @endphp

                            @if($startPage > 1)
                                <a href="{{ $tshirtImages->url(1) }}" class="inline-flex h-10 min-w-10 items-center justify-center rounded-lg border border-slate-200 bg-white px-3 text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">1</a>
                                @if($startPage > 2)
                                    <span class="pagination-muted px-2">...</span>
                                @endif
                            @endif

                            @for($page = $startPage; $page <= $endPage; $page++)
                                @if($page === $currentPage)
                                    <span class="inline-flex h-10 min-w-10 items-center justify-center rounded-lg border border-blue-600 bg-blue-600 px-3 font-semibold text-white dark:border-blue-400 dark:bg-blue-500 dark:text-white">{{ $page }}</span>
                                @else
                                    <a href="{{ $tshirtImages->url($page) }}" class="inline-flex h-10 min-w-10 items-center justify-center rounded-lg border border-slate-200 bg-white px-3 text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">{{ $page }}</a>
                                @endif
                            @endfor

                            @if($endPage < $lastPage)
                                @if($endPage < $lastPage - 1)
                                    <span class="pagination-muted px-2">...</span>
                                @endif
                                <a href="{{ $tshirtImages->url($lastPage) }}" class="inline-flex h-10 min-w-10 items-center justify-center rounded-lg border border-slate-200 bg-white px-3 text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">{{ $lastPage }}</a>
                            @endif

                            @if($tshirtImages->hasMorePages())
                                <a href="{{ $tshirtImages->nextPageUrl() }}" class="inline-flex h-10 min-w-10 items-center justify-center rounded-lg border border-slate-200 bg-white px-3 text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">&rsaquo;</a>
                            @else
                                <span class="pagination-muted inline-flex h-10 min-w-10 items-center justify-center rounded-lg border border-slate-200 bg-slate-100 px-3 dark:border-slate-700 dark:bg-slate-800">&rsaquo;</span>
                            @endif
                        </div>
                    </nav>
                @endif
            @endif
        </main>
    </div>
</div>
@endsection
