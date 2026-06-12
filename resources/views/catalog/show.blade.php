@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div>
                <img src="{{ asset('storage/tshirt_images/' . $tshirtImage->image_url) }}" alt="{{ $tshirtImage->name }}" class="w-full rounded-lg shadow-md">
            </div>
            <div>
                <h1 class="text-4xl font-bold mb-4">{{ $tshirtImage->name }}</h1>
                <p class="text-gray-700 text-lg mb-6">{{ $tshirtImage->description }}</p>

                <div class="bg-gray-100 p-6 rounded-lg">
                    <form action="{{ route('cart.add') }}" method="POST">
                        @csrf
                        <input type="hidden" name="tshirt_image_id" value="{{ $tshirtImage->id }}">

                        <div class="mb-4">
                            <label for="color" class="block text-gray-700 font-semibold mb-2">Cor:</label>
                            <select name="color_code" id="color" class="w-full border-gray-300 rounded-md shadow-sm">
                                <option value="#FFFFFF">Branco</option>
                                <option value="#000000">Preto</option>
                                <option value="#FF0000">Vermelho</option>
                                <option value="#0000FF">Azul</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="size" class="block text-gray-700 font-semibold mb-2">Tamanho:</label>
                            <select name="size" id="size" class="w-full border-gray-300 rounded-md shadow-sm">
                                <option value="XS">XS</option>
                                <option value="S">S</option>
                                <option value="M">M</option>
                                <option value="L">L</option>
                                <option value="XL">XL</option>
                            </select>
                        </div>

                        <div class="mb-6">
                            <label for="qty" class="block text-gray-700 font-semibold mb-2">Quantidade:</label>
                            <input type="number" name="qty" id="qty" value="1" min="1" class="w-full border-gray-300 rounded-md shadow-sm">
                        </div>

                        <button type="submit" class="w-full bg-blue-600 text-white font-bold py-3 px-6 rounded-md hover:bg-blue-700">Adicionar ao Carrinho</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="mt-12">
            <a href="{{ route('catalog.index') }}" class="text-blue-600 hover:underline">&larr; Voltar ao Catálogo</a>
        </div>
    </div>
@endsection
