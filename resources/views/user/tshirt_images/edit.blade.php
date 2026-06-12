@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-6">Editar Imagem</h1>

        <div class="bg-white rounded-lg shadow-md p-8 max-w-2xl mx-auto">
            <form action="{{ route('user.tshirt_images.update', $tshirt_image) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label for="name" class="block text-gray-700 font-semibold mb-2">Nome</label>
                    <input type="text" name="name" id="name" value="{{ $tshirt_image->name }}" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                </div>

                <div class="mb-6">
                    <label for="description" class="block text-gray-700 font-semibold mb-2">Descrição</label>
                    <textarea name="description" id="description" rows="3" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ $tshirt_image->description }}</textarea>
                </div>

                <div class="flex items-center justify-between">
                    <div>
                        <a href="{{ route('user.tshirt_images.index') }}" class="text-gray-600 hover:underline">Cancelar</a>
                    </div>
                    <div>
                        <button type="submit" class="bg-blue-600 text-white font-semibold px-4 py-2 rounded-md hover:bg-blue-700">Guardar Alterações</button>
                    </div>
                </div>
            </form>

            <div class="mt-8 border-t pt-6">
                <form action="{{ route('user.tshirt_images.destroy', $tshirt_image) }}" method="POST" onsubmit="return confirm('Tem a certeza que quer eliminar esta imagem?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 hover:underline">Eliminar Imagem</button>
                </form>
            </div>
        </div>
    </div>
@endsection
