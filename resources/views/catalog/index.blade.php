@extends('layouts.app') {{-- Assumindo que tens um layout base chamado app --}}

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6 text-center">Catálogo de T-Shirts</h1>

    {{-- Filtro / Pesquisa comum --}}
    <form action="{{ route('catalog.index') }}" method="GET" class="mb-8 flex flex-wrap gap-4 justify-center">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Pesquisar por nome ou descrição..."
               class="border rounded px-4 py-2 w-full max-w-md">

        <select name="category" class="border rounded px-4 py-2">
            <option value="">Todas as Categorias</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>

        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
            Filtrar
        </button>

        <a href="{{ route('catalog.index') }}" class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500 line-height">
            Limpar
        </a>
    </form>

    {{-- Lista de t-shirts (apenas imagens) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @forelse($tshirtImages as $image)
            <div class="border rounded-lg p-4 shadow hover:shadow-lg transition flex flex-col justify-between">
                <div>
                    <img src="{{ asset('storage/tshirt_images/' . $image->image_url) }}"
                         alt="{{ $image->name }}"
                         class="w-full h-48 object-contain mb-4 rounded">

                    <h2 class="font-bold text-lg mb-1">{{ $image->name }}</h2>
                    <p class="text-gray-600 text-sm mb-4">{{ Str::limit($image->description, 80) }}</p>
                </div>

                <div class="flex items-center justify-between">
                    <span class="text-xs bg-gray-200 text-gray-800 px-2 py-1 rounded">
                        {{ $image->category->name ?? 'Sem Categoria' }}
                    </span>

                    <div class="flex gap-2">
                        <a href="#" class="text-sm text-blue-600 hover:underline">Ver detalhes</a>
                        <form action="#" method="POST">
                            @csrf
                            <button class="bg-green-600 text-white px-3 py-1 rounded text-sm">Adicionar</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <p class="col-span-full text-center text-gray-500 py-12">Nenhuma t-shirt encontrada com os filtros selecionados.</p>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $tshirtImages->links() }}
    </div>
</div>
@endsection
