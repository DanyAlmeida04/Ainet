@extends('layouts.admin')

@section('admin-content')
{{-- Alerts & Notifications --}}
@if(session('success'))
    <div class="mb-6 flex items-center gap-3 p-4 bg-emerald-50 dark:bg-emerald-950/20 text-emerald-800 dark:text-emerald-300 rounded-xl border border-emerald-200/60 dark:border-emerald-900/60 shadow-sm animate-fade-in">
        <svg class="w-5 h-5 flex-shrink-0 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span class="text-sm font-semibold">{{ session('success') }}</span>
    </div>
@endif

@if(session('info'))
    <div class="mb-6 flex items-center gap-3 p-4 bg-blue-50 dark:bg-blue-950/20 text-blue-800 dark:text-blue-300 rounded-xl border border-blue-200/60 dark:border-blue-900/60 shadow-sm">
        <svg class="w-5 h-5 flex-shrink-0 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span class="text-sm font-semibold">{{ session('info') }}</span>
    </div>
@endif

@if($errors->any())
    <div class="mb-6 p-4 bg-rose-50 dark:bg-rose-950/20 text-rose-800 dark:text-rose-300 rounded-xl border border-rose-200/60 dark:border-rose-900/60 shadow-sm">
        <div class="flex items-center gap-3 mb-2">
            <svg class="w-5 h-5 text-rose-600 dark:text-rose-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <span class="text-sm font-bold">Ocorreram erros:</span>
        </div>
        <ul class="list-disc list-inside text-xs space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200/60 dark:border-slate-800 overflow-hidden">
    <div class="p-6 border-b border-slate-200/60 dark:border-slate-800">
        <div>
            <h2 class="text-xl font-bold text-slate-800 dark:text-white">Gestão de Encomendas</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Acompanhe e faça a gestão do estado e dos recibos de todas as encomendas efetuadas.</p>
        </div>

        {{-- Filters Form --}}
        <form method="get" action="{{ route('admin.orders.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 items-end mt-6">
            <div>
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">ID do Cliente</label>
                <input type="text" name="customer_id" placeholder="Ex: 5" value="{{ request('customer_id') }}"
                       class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-900 dark:text-slate-200">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">T-shirt (ID ou Nome)</label>
                <input type="text" name="tshirt" placeholder="Nome do design..." value="{{ request('tshirt') }}"
                       class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-900 dark:text-slate-200">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Estado</label>
                <select name="status" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-900 dark:text-slate-200">
                    <option value="">Todos os Estados</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pendente</option>
                    <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Fechada / Paga</option>
                    <option value="canceled" {{ request('status') === 'canceled' ? 'selected' : '' }}>Anulada</option>
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-blue-600 text-white font-semibold py-2.5 px-4 rounded-xl text-sm hover:bg-blue-700 transition duration-150 shadow-sm cursor-pointer">
                    Filtrar
                </button>
                @if(request()->anyFilled(['customer_id', 'tshirt', 'status']) || request('all') == '1')
                    <a href="{{ route('admin.orders.index') }}" class="bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-semibold py-2.5 px-4 rounded-xl text-sm hover:bg-slate-200 dark:hover:bg-slate-700 transition text-center flex items-center justify-center">
                        Limpar
                    </a>
                @else
                    <a href="?all=1" class="bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-semibold py-2.5 px-4 rounded-xl text-sm hover:bg-slate-200 dark:hover:bg-slate-700 transition text-center flex items-center justify-center" title="Ver tudo sem paginação">
                        Ver Todas
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Orders Table --}}
    <div class="overflow-x-auto">
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-slate-50 dark:bg-slate-800/30 border-b border-slate-100 dark:border-slate-800/60">
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-24">ID</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Cliente</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-36">Data</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-32">Total</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-36">Estado</th>
                    <th class="px-6 py-4 text-center text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-48">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                @forelse($orders as $o)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/10 transition-colors duration-100">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-slate-500 dark:text-slate-400">
                            #{{ $o->id }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($o->customer && $o->customer->user)
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0 w-9 h-9 rounded-full overflow-hidden border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
                                        @if($o->customer->user->photo_url)
                                            <img src="{{ asset('storage/photos/' . $o->customer->user->photo_url) }}" alt="Avatar" class="w-full h-full object-cover">
                                        @else
                                            <span class="text-xs font-bold text-slate-400 uppercase">{{ substr($o->customer->user->name, 0, 2) }}</span>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-slate-800 dark:text-slate-200 leading-tight">
                                            {{ $o->customer->user->name }}
                                        </div>
                                        <div class="text-xs text-slate-500 dark:text-slate-400">
                                            {{ $o->customer->user->email }}
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="text-sm font-medium text-slate-500 dark:text-slate-400">
                                    Cliente #{{ $o->customer_id }} (Sem perfil ativo)
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600 dark:text-slate-300">
                            {{ $o->date->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-slate-800 dark:text-slate-200">
                            {{ number_format($o->total_price, 2) }} €
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($o->status === 'closed')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 dark:bg-emerald-950/30 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-900/40">
                                    Fechada
                                </span>
                            @elseif($o->status === 'canceled')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-rose-50 text-rose-700 dark:bg-rose-950/30 dark:text-rose-400 border border-rose-100 dark:border-rose-900/40">
                                    Anulada
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-700 dark:bg-amber-950/30 dark:text-amber-400 border border-amber-100 dark:border-amber-900/40 animate-pulse">
                                    Pendente
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-semibold">
                            <div class="flex items-center justify-center gap-1">
                                <a href="{{ route('admin.orders.show', $o) }}" class="inline-flex items-center justify-center p-2 rounded-lg text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-blue-600 dark:hover:text-blue-400 transition" title="Ver Detalhes">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>

                                @if($o->status === 'pending')
                                    <form action="{{ route('admin.orders.close', $o) }}" method="post" class="inline">
                                        @csrf
                                        <button class="inline-flex items-center justify-center p-2 rounded-lg text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-emerald-600 dark:hover:text-emerald-400 transition cursor-pointer" title="Marcar como Fechada">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </button>
                                    </form>

                                    <form action="{{ route('admin.orders.cancel', $o) }}" method="post" class="inline" onsubmit="return confirm('Tem a certeza que deseja anular a encomenda #{{ $o->id }}?');">
                                        @csrf
                                        <button class="inline-flex items-center justify-center p-2 rounded-lg text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-rose-600 dark:hover:text-rose-400 transition cursor-pointer" title="Anular Encomenda">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-sm text-slate-500 dark:text-slate-400">
                            Nenhuma encomenda encontrada com os filtros especificados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if(method_exists($orders, 'links'))
        <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/10 border-t border-slate-100 dark:border-slate-800/60">
            {{ $orders->links() }}
        </div>
    @endif
</div>
@endsection
