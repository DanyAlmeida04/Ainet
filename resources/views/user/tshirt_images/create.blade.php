@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-6">Enviar Nova Imagem</h1>

        <div class="bg-white rounded-lg shadow-md p-8 max-w-2xl mx-auto">
            <form action="{{ route('user.tshirt_images.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-4">
                    <label for="name" class="block text-gray-700 font-semibold mb-2">Nome</label>
                    <input type="text" name="name" id="name" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                </div>

                <div class="mb-4">
                    <label for="description" class="block text-gray-700 font-semibold mb-2">Descrição</label>
                    <textarea name="description" id="description" rows="3" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                </div>

                <div class="mb-6">
                    <label for="image" class="block text-gray-700 font-semibold mb-2">Ficheiro de Imagem</label>
                    <input type="file" name="image" id="image" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                </div>

                <div class="flex items-center justify-end">
                    <a href="{{ route('user.tshirt_images.index') }}" class="text-gray-600 hover:underline mr-4">Cancelar</a>
                    <button type="submit" class="bg-blue-600 text-white font-semibold px-4 py-2 rounded-md hover:bg-blue-700">Enviar Imagem</button>
                </div>
            </form>
        </div>
    </div>
@endsection
