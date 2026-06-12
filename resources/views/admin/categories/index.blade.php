@extends('layouts.admin')

@section('admin-content')
{{-- Alert Success Notification --}}
@if(session('success'))
    <div class="mb-6 flex items-center gap-3 p-4 bg-emerald-50 dark:bg-emerald-950/20 text-emerald-800 dark:text-emerald-300 rounded-xl border border-emerald-200/60 dark:border-emerald-900/60 shadow-sm animate-fade-in">
        <svg class="w-5 h-5 flex-shrink-0 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span class="text-sm font-semibold">{{ session('success') }}</span>
    </div>
@endif

<div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200/60 dark:border-slate-800 overflow-hidden">
    <div class="p-6 border-b border-slate-200/60 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800 dark:text-white">Categorias de Desenho</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Gerencie as categorias associadas aos designs de t-shirt.</p>
        </div>
        <a href="{{ route('admin.categories.create') }}" class="inline-flex items-center justify-center gap-1.5 bg-blue-600 text-white font-semibold py-2.5 px-4 rounded-xl text-sm hover:bg-blue-700 hover:shadow-md transition duration-150 shadow-sm text-center">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
            Nova Categoria
        </a>
    </div>

    {{-- Categories Table --}}
    <div class="overflow-x-auto">
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-slate-50 dark:bg-slate-800/30 border-b border-slate-100 dark:border-slate-800/60">
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-24">ID</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Nome da Categoria</th>
                    <th class="px-6 py-4 text-center text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-36">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                @forelse($categories as $c)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/10 transition-colors duration-100">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-slate-500 dark:text-slate-400">
                            #{{ $c->id }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-slate-800 dark:text-slate-200">
                            {{ $c->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-semibold">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.categories.edit', $c) }}" class="inline-flex items-center justify-center p-2 rounded-lg text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-blue-600 dark:hover:text-blue-400 transition" title="Editar">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-2.036a5 5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </a>

                                <form action="{{ route('admin.categories.destroy', $c) }}" method="post" class="inline" onsubmit="return confirm('Tem a certeza que deseja eliminar a categoria {{ $c->name }}? Todos os designs associados poderão ficar sem categoria.');">
                                    @csrf
                                    <button class="inline-flex items-center justify-center p-2 rounded-lg text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-red-600 dark:hover:text-red-400 transition cursor-pointer" title="Eliminar">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-6 py-10 text-center text-sm text-slate-500 dark:text-slate-400">
                            Nenhuma categoria registada.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if(method_exists($categories, 'links'))
        <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/10 border-t border-slate-100 dark:border-slate-800/60">
            {{ $categories->links() }}
        </div>
    @endif
</div>
@endsection
