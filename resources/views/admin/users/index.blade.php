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
    <div class="p-6 border-b border-slate-200/60 dark:border-slate-800">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
            <div>
                <h2 class="text-xl font-bold text-slate-800 dark:text-white">Gestão de Utilizadores</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Visualize, filtre, edite e controle o estado dos utilizadores registados na FunShirt.</p>
            </div>
            <div>
                <a href="{{ route('admin.users.create') }}" class="inline-flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 px-4 rounded-xl text-sm transition shadow-sm hover:no-underline cursor-pointer">
                    <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Novo Utilizador
                </a>
            </div>
        </div>

        {{-- Filters Section --}}
        <form method="get" action="{{ route('admin.users.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 items-end">
            <div>
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Procurar</label>
                <input type="text" name="search" placeholder="Nome ou Email..." value="{{ request('search') }}"
                       class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-900 dark:text-slate-200">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Tipo de Utilizador</label>
                <select name="user_type" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-900 dark:text-slate-200">
                    <option value="">Todos os Tipos</option>
                    <option value="C" {{ request('user_type') === 'C' ? 'selected' : '' }}>Cliente</option>
                    <option value="E" {{ request('user_type') === 'E' ? 'selected' : '' }}>Funcionário</option>
                    <option value="A" {{ request('user_type') === 'A' ? 'selected' : '' }}>Administrador</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Estado</label>
                <select name="blocked" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-900 dark:text-slate-200">
                    <option value="">Todos os Estados</option>
                    <option value="0" {{ request('blocked') === '0' ? 'selected' : '' }}>Ativo</option>
                    <option value="1" {{ request('blocked') === '1' ? 'selected' : '' }}>Bloqueado</option>
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-blue-600 text-white font-semibold py-2.5 px-4 rounded-xl text-sm hover:bg-blue-700 transition duration-150 shadow-sm cursor-pointer">
                    Filtrar
                </button>
                @if(request()->anyFilled(['search', 'user_type', 'blocked']))
                    <a href="{{ route('admin.users.index') }}" class="bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-semibold py-2.5 px-4 rounded-xl text-sm hover:bg-slate-200 dark:hover:bg-slate-700 transition duration-150 text-center flex items-center justify-center">
                        Limpar
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Users Table --}}
    <div class="overflow-x-auto">
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-slate-50 dark:bg-slate-800/30 border-b border-slate-100 dark:border-slate-800/60">
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-16">ID</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Utilizador</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-36">Tipo</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-32">Estado</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-44">Bloqueado em</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-40">Registo</th>
                    <th class="px-6 py-4 text-center text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-48">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                @forelse($users as $u)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/10 transition-colors duration-100">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-slate-500 dark:text-slate-400">
                            #{{ $u->id }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0 w-10 h-10 rounded-full overflow-hidden border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
                                    @if($u->photo_url)
                                        <img src="{{ asset('storage/photos/' . $u->photo_url) }}" alt="Foto de {{ $u->name }}" class="w-full h-full object-cover">
                                    @else
                                        <span class="text-sm font-bold text-slate-400 uppercase">{{ substr($u->name, 0, 2) }}</span>
                                    @endif
                                </div>
                                <div>
                                    <div class="text-sm font-semibold text-slate-800 dark:text-slate-200 leading-tight">
                                        {{ $u->name }}
                                    </div>
                                    <div class="text-xs text-slate-500 dark:text-slate-400">
                                        {{ $u->email }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($u->user_type === 'A')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-50 text-red-700 dark:bg-red-950/30 dark:text-red-400 border border-red-100 dark:border-red-900/40">
                                    Administrador
                                </span>
                            @elseif($u->user_type === 'E' || $u->user_type === 'F')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 dark:bg-blue-950/30 dark:text-blue-400 border border-blue-100 dark:border-blue-900/40">
                                    Funcionário
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-50 text-slate-600 dark:bg-slate-800 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                    Cliente
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($u->blocked)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-rose-50 text-rose-700 dark:bg-rose-950/30 dark:text-rose-400 border border-rose-100 dark:border-rose-900/40">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-600 dark:bg-rose-500"></span>
                                    Bloqueado
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 dark:bg-emerald-950/30 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-900/40">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 dark:bg-emerald-500"></span>
                                    Ativo
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400">
                            @if($u->blocked)
                                <span class="font-medium text-slate-700 dark:text-slate-300">
                                    {{ $u->updated_at ? $u->updated_at->format('d/m/Y H:i') : '—' }}
                                </span>
                            @else
                                <span class="text-slate-400 dark:text-slate-600">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400">
                            {{ $u->created_at ? $u->created_at->format('d/m/Y') : '—' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-semibold">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.users.edit', $u) }}" class="inline-flex items-center justify-center p-2 rounded-lg text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-blue-600 dark:hover:text-blue-400 transition" title="Editar">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-2.036a5 5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </a>

                                <form action="{{ route('admin.users.toggleBlock', $u) }}" method="post" class="inline">
                                    @csrf
                                    <button class="inline-flex items-center justify-center p-2 rounded-lg text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 {{ $u->blocked ? 'hover:text-emerald-600 dark:hover:text-emerald-400' : 'hover:text-amber-600 dark:hover:text-amber-400' }} transition cursor-pointer" 
                                            title="{{ $u->blocked ? 'Desbloquear' : 'Bloquear' }}">
                                        @if($u->blocked)
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z" />
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                            </svg>
                                        @endif
                                    </button>
                                </form>

                                <form action="{{ route('admin.users.destroy', $u) }}" method="post" class="inline" onsubmit="return confirm('Tem a certeza que deseja eliminar o utilizador {{ $u->name }}?');">
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
                        <td colspan="7" class="px-6 py-10 text-center text-sm text-slate-500 dark:text-slate-400">
                            Nenhum utilizador encontrado com os filtros selecionados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if(method_exists($users, 'links'))
        <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/10 border-t border-slate-100 dark:border-slate-800/60">
            {{ $users->links() }}
        </div>
    @endif
</div>
@endsection
