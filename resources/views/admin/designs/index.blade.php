@extends('layouts.admin')

@section('admin-content')
<div class="mt-4">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold text-slate-800 dark:text-white">Gerir Designs do Catálogo</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">Total: {{ $designs->total() }} designs disponíveis</p>
        </div>
        <a href="{{ route('admin.designs.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 px-4 rounded-xl text-xs shadow-sm transition flex items-center gap-1.5 cursor-pointer hover:no-underline">
            <span>+</span> Novo Design
        </a>
    </div>

    {{-- Session Notices --}}
    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-950/20 text-emerald-800 dark:text-emerald-300 rounded-xl border border-emerald-250/50 dark:border-emerald-900/60 shadow-sm text-sm font-semibold">
            {{ session('success') }}
        </div>
    @endif

    {{-- Filters --}}
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/60 dark:border-slate-800 p-4 mb-6 shadow-sm">
        <form action="{{ route('admin.designs.index') }}" method="GET" class="flex flex-col md:flex-row gap-3">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Pesquisar por nome ou descrição..." 
                       class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="w-full md:w-48">
                <select name="category" class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Todas as Categorias</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-xl text-xs cursor-pointer transition">
                    Filtrar
                </button>
                @if(request()->anyFilled(['search', 'category']))
                    <a href="{{ route('admin.designs.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-800 dark:hover:bg-slate-700/80 dark:text-slate-300 font-semibold px-4 py-2 rounded-xl text-xs transition flex items-center justify-center hover:no-underline">
                        Limpar
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Grid --}}
    @if($designs->isEmpty())
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/60 dark:border-slate-800 p-12 text-center shadow-sm">
            <p class="text-sm text-slate-500 dark:text-slate-400">Nenhum design do catálogo encontrado.</p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($designs as $design)
                <div class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-2xl overflow-hidden hover:shadow-md transition-shadow duration-200 flex flex-col justify-between">
                    
                    {{-- Thumbnail --}}
                    <div class="aspect-square bg-slate-50 dark:bg-slate-950 flex items-center justify-center p-6 border-b border-slate-100 dark:border-slate-850 relative group">
                        <img src="{{ asset('storage/tshirt_images/' . $design->image_url) }}" alt="{{ $design->name }}" class="max-h-full max-w-full object-contain drop-shadow-sm">
                        
                        {{-- Hover preview link to catalog show page --}}
                        <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                            <a href="{{ route('catalog.show', $design) }}" target="_blank" class="bg-white text-slate-950 text-xs font-semibold py-1.5 px-3.5 rounded-lg shadow hover:bg-slate-50 hover:no-underline">
                                Ver na Loja ↗
                            </a>
                        </div>
                    </div>

                    {{-- Info --}}
                    <div class="p-4 flex-1 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-1.5">
                                <span class="text-[10px] bg-blue-50 text-blue-800 dark:bg-blue-950/60 dark:text-blue-300 font-bold px-2 py-0.5 rounded-full uppercase truncate">
                                    {{ $design->category->name ?? 'Sem Categoria' }}
                                </span>
                                <span class="text-[9px] text-slate-400 font-semibold">ID: #{{ $design->id }}</span>
                            </div>
                            <h4 class="font-bold text-slate-800 dark:text-white text-sm truncate" title="{{ $design->name }}">
                                {{ $design->name }}
                            </h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 line-clamp-2 leading-relaxed h-8">
                                {{ $design->description ?: 'Sem descrição.' }}
                            </p>
                        </div>

                        <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between">
                            <a href="{{ route('admin.designs.edit', $design) }}" class="text-xs font-semibold text-blue-650 hover:text-blue-700 dark:text-blue-400 hover:underline">
                                Editar
                            </a>

                            <form action="{{ route('admin.designs.destroy', $design) }}" method="POST" onsubmit="return confirm('Tem a certeza que deseja eliminar este design do catálogo? (soft-delete)');">
                                @csrf
                                <button type="submit" class="text-xs font-semibold text-rose-600 hover:text-rose-700 cursor-pointer">
                                    Eliminar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($designs->hasPages())
            <div class="mt-8">
                {{ $designs->links() }}
            </div>
        @endif
    @endif
</div>
@endsection
