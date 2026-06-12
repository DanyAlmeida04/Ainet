@extends('layouts.admin')

@section('admin-content')
<div class="bg-white dark:bg-slate-900 rounded-2xl shadow border border-slate-200/60 dark:border-slate-800 p-6">
    <h2 class="text-xl font-bold text-slate-800 dark:text-white mb-6">Editar Categoria — {{ $category->name }}</h2>

    <form method="post" enctype="multipart/form-data" action="{{ route('admin.categories.update', $category) }}" class="space-y-4 max-w-md">
        @csrf
        <div>
            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Nome</label>
            <input name="name" required value="{{ old('name', $category->name) }}" class="w-full border border-slate-200 dark:border-slate-700 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-slate-800 dark:text-white">
        </div>
        <div>
            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Imagem (opcional)</label>
            @if($category->image_url)
                <div class="mb-2">
                    <img src="{{ asset('storage/categories/' . $category->image_url) }}" alt="{{ $category->name }}" class="w-16 h-16 object-cover rounded-lg border border-slate-200 dark:border-slate-700">
                </div>
            @endif
            <input type="file" name="image" class="block w-full text-sm text-slate-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
        </div>
        <div class="flex gap-3 pt-2">
            <button class="bg-blue-600 text-white px-5 py-2 rounded-lg font-semibold hover:bg-blue-700 transition">Guardar</button>
            <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 text-sm text-slate-600 hover:underline">Cancelar</a>
        </div>
    </form>
</div>
@endsection
