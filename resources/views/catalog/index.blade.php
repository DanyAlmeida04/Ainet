@extends('layouts.app') {{-- Assumindo que tens um layout base chamado app --}}

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold">Catálogo de T-Shirts</h1>
        @can('manage-users')
            <a href="{{ route('admin.dashboard') }}" class="ml-4 px-4 py-2 bg-white text-blue-800 rounded shadow hover:bg-gray-100">Admin</a>
        @endcan
    </div>

    <div class="flex gap-6">
        {{-- Sidebar: lista de categorias --}}
        <aside class="w-64 bg-white rounded shadow p-4">
            <h2 class="font-semibold mb-3">Categorias</h2>
            <ul class="space-y-2">
                <li>
                    <a href="{{ route('catalog.index') }}" class="block px-3 py-2 rounded {{ request('category') ? 'hover:bg-gray-100' : 'bg-blue-50 font-semibold' }}">Todas as categorias</a>
                </li>
                @foreach($categories as $cat)
                    <li>
                        <a href="{{ route('catalog.index', array_merge(request()->query(), ['category' => $cat->id])) }}"
                           class="flex items-center gap-3 px-3 py-2 rounded hover:bg-gray-100 {{ request('category') == $cat->id ? 'bg-blue-50 font-semibold' : '' }}">
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
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Pesquisar</button>
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
                        <div class="border rounded-lg p-4 shadow hover:shadow-lg transition flex flex-col justify-between">
                            <div>
                                <img src="{{ asset('storage/tshirt_images/' . $image->image_url) }}" alt="{{ $image->name }}" class="w-full h-48 object-contain mb-4 rounded">
                                <h2 class="font-bold text-lg mb-1">{{ $image->name }}</h2>
                                <p class="text-gray-600 text-sm mb-4">{{ Str::limit($image->description, 80) }}</p>
                            </div>

                            <div class="flex items-center justify-between">
                                <span class="text-xs bg-gray-200 text-gray-800 px-2 py-1 rounded">{{ $image->category->name ?? 'Sem Categoria' }}</span>
                                <div class="flex gap-2">
                                    <a href="#" class="text-sm text-blue-600 hover:underline">Ver detalhes</a>
                                    <form action="{{ route('cart.add') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="tshirt_image_id" value="{{ $image->id }}">
                                        @php $defaultColor = \App\Models\Color::first()?->code ?? 'white'; @endphp
                                        <input type="hidden" name="color_code" value="{{ $defaultColor }}">
                                        <input type="hidden" name="size" value="M">
                                        <input type="hidden" name="qty" value="1">
                                        <button class="bg-green-600 text-white px-3 py-1 rounded text-sm">Adicionar</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $tshirtImages->links() }}
                </div>
            @endif
        </main>
    </div>
</div>
@endsection
