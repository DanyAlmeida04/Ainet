@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
        <div class="md:col-span-1">
            <h2 class="text-xl font-bold mb-4">Filtros</h2>
            <form method="GET" action="{{ route('catalog.index') }}">
                <div class="mb-4">
                    <label for="search" class="block text-sm font-medium text-gray-700">Pesquisa</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div class="mb-4">
                    <label for="category" class="block text-sm font-medium text-gray-700">Categoria</label>
                    <select name="category" id="category" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="">Todas</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md">Filtrar</button>
            </form>
        </div>

        <div class="md:col-span-3">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($tshirtImages as $image)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden transform transition duration-300 hover:scale-105">
                        <img src="{{ asset('storage/tshirt_images/' . $image->image_url) }}" alt="{{ $image->name }}" class="w-full h-64 object-cover">
                        <div class="p-4">
                            <h3 class="text-lg font-semibold">{{ $image->name }}</h3>
                            <p class="text-gray-600 text-sm mb-4">{{ Str::limit($image->description, 50) }}</p>
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-lg">€{{-- A lógica de preço será adicionada aqui --}}</span>
                                <a href="{{ route('catalog.show', $image) }}" class="bg-blue-500 text-white px-3 py-1 rounded-md text-sm font-semibold hover:bg-blue-600">Ver detalhes</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <p class="text-gray-500 text-lg">Não foram encontradas t-shirts com os filtros selecionados.</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-8">
                {{ $tshirtImages->links('pagination.tailwind') }}
            </div>
        </div>
    </div>
</div>
@endsection
