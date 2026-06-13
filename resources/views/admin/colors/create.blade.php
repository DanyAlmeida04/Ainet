@extends('layouts.admin')

@section('admin-content')
<div class="max-w-2xl mx-auto bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200/60 dark:border-slate-800 p-6 sm:p-8">
    <div class="flex items-center justify-between mb-6 border-b border-slate-100 dark:border-slate-800 pb-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800 dark:text-white">Criar Nova Cor</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Registe uma nova cor de t-shirt para venda e envie a imagem de base.</p>
        </div>
        <a href="{{ route('admin.colors.index') }}" class="text-sm font-medium text-slate-500 hover:text-blue-600 hover:underline dark:text-slate-400 dark:hover:text-blue-400 transition-colors">
            Cancelar
        </a>
    </div>

    <form method="post" enctype="multipart/form-data" action="{{ route('admin.colors.store') }}" class="space-y-6">
        @csrf
        
        <div>
            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Código da Cor (CSS / Hex)</label>
            <input type="text" name="code" required value="{{ old('code') }}" placeholder="Ex: white, black, steelblue, #7fffd4, #ab0034"
                   class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-900 dark:text-slate-200">
            <p class="text-[11px] text-slate-400 mt-1">Este código deve corresponder a uma propriedade de cor CSS válida para renderizar as amostras visuais.</p>
            @error('code')
                <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Nome da Cor</label>
            <input type="text" name="name" required value="{{ old('name') }}" placeholder="Ex: Branco, Preto, Azul Celeste..."
                   class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-900 dark:text-slate-200">
            @error('name')
                <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">T-Shirt de Base (Sem Estampagem)</label>
            <input type="file" name="image" required
                   class="block w-full text-sm text-slate-500 dark:text-slate-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-blue-950/30 dark:file:text-blue-400 dark:hover:file:bg-blue-900/40 transition file:cursor-pointer">
            <p class="text-[11px] text-slate-400 mt-1">A imagem será armazenada no servidor com o nome do código da cor (ex: <code class="font-mono bg-slate-100 dark:bg-slate-800 px-1 py-0.5 rounded">&lt;codigo&gt;.jpg</code>).</p>
            @error('image')
                <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
            <button type="submit" class="bg-blue-600 text-white font-semibold py-2.5 px-6 rounded-xl text-sm hover:bg-blue-700 hover:shadow-md transition duration-150 cursor-pointer shadow-sm">
                Criar Cor
            </button>
            <a href="{{ route('admin.colors.index') }}" class="bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-semibold py-2.5 px-6 rounded-xl text-sm hover:bg-slate-200 dark:hover:bg-slate-700 transition text-center">
                Cancelar
            </a>
        </div>
    </form>
</div>
@endsection
