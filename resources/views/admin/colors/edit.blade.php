@extends('layouts.admin')

@section('admin-content')
<div class="max-w-2xl mx-auto bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200/60 dark:border-slate-800 p-6 sm:p-8">
    <div class="flex items-center justify-between mb-6 border-b border-slate-100 dark:border-slate-800 pb-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800 dark:text-white">Editar Cor</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Modifique o nome ou a imagem de base da cor {{ $color->name }}.</p>
        </div>
        <a href="{{ route('admin.colors.index') }}" class="text-sm font-medium text-slate-500 hover:text-blue-600 hover:underline dark:text-slate-400 dark:hover:text-blue-400 transition-colors">
            Cancelar
        </a>
    </div>

    <form method="post" enctype="multipart/form-data" action="{{ route('admin.colors.update', $color) }}" class="space-y-6">
        @csrf
        
        <div>
            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Código da Cor (Não Editável)</label>
            <div class="flex items-center gap-3">
                <input type="text" disabled value="{{ $color->code }}"
                       class="w-full px-3.5 py-2.5 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-500 dark:text-slate-400 cursor-not-allowed font-mono">
                <span class="inline-block w-10 h-10 rounded-full border border-slate-200 shadow-sm flex-shrink-0" style="background-color: {{ $color->code }};"></span>
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Nome da Cor</label>
            <input type="text" name="name" required value="{{ old('name', $color->name) }}" placeholder="Ex: Branco, Preto, Azul Celeste..."
                   class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-900 dark:text-slate-200">
            @error('name')
                <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">T-Shirt de Base (Sem Estampagem)</label>
            @php
                $imagePath = 'storage/tshirt_base/' . $color->code . '.jpg';
                $fileExists = file_exists(public_path($imagePath));
            @endphp
            @if($fileExists)
                <div class="mb-4 flex items-center gap-3">
                    <div class="w-20 h-20 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm bg-slate-50 dark:bg-slate-850 p-2 flex items-center justify-center overflow-hidden">
                        <img src="{{ asset($imagePath) }}?v={{ time() }}" alt="{{ $color->name }}" 
                             class="max-w-full max-h-full object-contain">
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-slate-400 block">Imagem Atual</span>
                        <span class="text-xs text-slate-500 dark:text-slate-400 font-mono">{{ $color->code }}.jpg</span>
                    </div>
                </div>
            @endif

            <input type="file" name="image" 
                   class="block w-full text-sm text-slate-500 dark:text-slate-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-blue-950/30 dark:file:text-blue-400 dark:hover:file:bg-blue-900/40 transition file:cursor-pointer">
            <p class="text-[11px] text-slate-400 mt-1">Selecione uma nova imagem apenas se desejar substituir a imagem atual.</p>
            @error('image')
                <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
            <button type="submit" class="bg-blue-600 text-white font-semibold py-2.5 px-6 rounded-xl text-sm hover:bg-blue-700 hover:shadow-md transition duration-150 cursor-pointer shadow-sm">
                Guardar Alterações
            </button>
            <a href="{{ route('admin.colors.index') }}" class="bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-semibold py-2.5 px-6 rounded-xl text-sm hover:bg-slate-200 dark:hover:bg-slate-700 transition text-center">
                Cancelar
            </a>
        </div>
    </form>
</div>
@endsection
