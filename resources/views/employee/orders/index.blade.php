@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    
    {{-- Alert Success Notification --}}
    @if(session('success'))
        <div class="mb-6 flex items-center gap-3 p-4 bg-emerald-50 dark:bg-emerald-950/20 text-emerald-800 dark:text-emerald-300 rounded-xl border border-emerald-200/60 dark:border-emerald-900/60 shadow-sm animate-fade-in">
            <svg class="w-5 h-5 flex-shrink-0 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="text-sm font-semibold">{{ session('success') }}</span>
        </div>
    @endif

    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200/60 dark:border-slate-800 overflow-hidden mb-6">
        <div class="p-6 border-b border-slate-200/60 dark:border-slate-800">
            <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Fila de Encomendas Pendentes</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Área de Logística. Verifique, estampe e envie as t-shirts pedidas pelos clientes.</p>
        </div>

        {{-- Pending Orders Table --}}
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/30 border-b border-slate-100 dark:border-slate-800/60">
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-24">ID</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Cliente</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-36">Data de Pedido</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-36">Total Cobrado</th>
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
                                        Cliente #{{ $o->customer_id }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600 dark:text-slate-300">
                                {{ $o->date->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-slate-800 dark:text-white">
                                {{ number_format($o->total_price, 2) }} €
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-semibold">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('employee.orders.show', $o) }}" 
                                       class="inline-flex items-center justify-center px-3 py-1.5 bg-blue-50 hover:bg-blue-100 dark:bg-blue-950/20 dark:hover:bg-blue-900/30 text-blue-700 dark:text-blue-400 rounded-lg text-xs font-bold transition shadow-sm"
                                       title="Ver Detalhes e Artigos">
                                        Ver Artigos
                                    </a>
                                    
                                    <form action="{{ route('employee.orders.close', $o) }}" method="post" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                class="inline-flex items-center justify-center px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold transition shadow-sm cursor-pointer" 
                                                title="Marcar como Fechada/Expedida">
                                            Concluir & Enviar
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-sm text-slate-500 dark:text-slate-400">
                                Nenhuma encomenda pendente para processamento logístico. Bom trabalho!
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
</div>
@endsection
