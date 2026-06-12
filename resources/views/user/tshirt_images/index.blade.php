@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold">As Minhas Imagens</h1>
            <a href="{{ route('user.tshirt_images.create') }}" class="bg-blue-600 text-white font-semibold px-4 py-2 rounded-md hover:bg-blue-700">Enviar Nova Imagem</a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @forelse($tshirt_images as $image)
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <img src="{{ asset('storage/tshirt_images_private/' . $image->image_url) }}" alt="{{ $image->name }}" class="w-full h-48 object-cover">
                    <div class="p-4">
                        <h3 class="font-semibold text-lg">{{ $image->name }}</h3>
                        <p class="text-gray-600 text-sm mb-4">{{ $image->description }}</p>
                        <div class="flex items-center justify-between">
                            <a href="{{ route('user.tshirt_images.edit', $image) }}" class="text-blue-600 hover:underline">Editar</a>
                            <form action="{{ route('cart.add') }}" method="POST">
                                @csrf
                                <input type="hidden" name="tshirt_image_id" value="{{ $image->id }}">
                                <input type="hidden" name="is_custom" value="1">
                                <input type="hidden" name="color_code" value="#FFFFFF">
                                <input type="hidden" name="size" value="M">
                                <input type="hidden" name="qty" value="1">
                                <button type="submit" class="bg-green-600 text-white font-semibold px-3 py-1 rounded-md text-sm hover:bg-green-700">Adicionar ao Carrinho</button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center text-gray-500">
                    <p>Ainda não tem imagens personalizadas.</p>
                    <a href="{{ route('user.tshirt_images.create') }}" class="mt-4 inline-block bg-blue-600 text-white font-semibold px-4 py-2 rounded-md hover:bg-blue-700">Envie a sua primeira imagem</a>
                </div>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $tshirt_images->links('pagination.tailwind') }}
        </div>
    </div>
@endsection
