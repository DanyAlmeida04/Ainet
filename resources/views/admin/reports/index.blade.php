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

@if($errors->any())
    <div class="mb-6 p-4 bg-rose-50 dark:bg-rose-950/20 text-rose-800 dark:text-rose-300 rounded-xl border border-rose-200/60 dark:border-rose-900/60 shadow-sm">
        <ul class="list-disc list-inside text-xs space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200/60 dark:border-slate-800 overflow-hidden">
    <div class="p-6 border-b border-slate-200/60 dark:border-slate-800">
        <h2 class="text-xl font-bold text-slate-800 dark:text-white">Reportes de Recibos</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Modere pedidos de regeneração e reenvio de recibos submetidos pelos clientes.</p>
    </div>

    @if($reports->isEmpty())
        <div class="p-12 text-center text-slate-500 dark:text-slate-400">
            <svg class="w-12 h-12 mx-auto text-slate-300 dark:text-slate-650 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <p class="text-base font-semibold">Nenhum reporte registado.</p>
            <p class="text-xs text-slate-400 mt-1">De momento, não existem pedidos de suporte ou problemas reportados nos recibos.</p>
        </div>
    @else
        {{-- Reports Table --}}
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/30 border-b border-slate-100 dark:border-slate-800/60">
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-24">ID</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-36">Encomenda</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Cliente</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-40">Data de Reporte</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-36">Estado</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-56">Ações Moderador</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                    @foreach($reports as $rep)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/10 transition-colors duration-100">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-slate-500 dark:text-slate-400">
                                #{{ $rep->id }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-1.5">
                                    <span class="text-sm font-semibold text-slate-700 dark:text-slate-300">#{{ $rep->order_id }}</span>
                                    <a href="{{ route('admin.orders.show', $rep->order_id) }}" class="p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-blue-600 dark:text-blue-400 transition" title="Ver Detalhes da Encomenda">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($rep->user)
                                    <div class="text-sm font-semibold text-slate-800 dark:text-slate-200 leading-tight">
                                        {{ $rep->user->name }}
                                    </div>
                                    <div class="text-xs text-slate-500 dark:text-slate-400">
                                        {{ $rep->user->email }}
                                    </div>
                                @else
                                    <span class="text-sm text-slate-400">Utilizador desconhecido</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600 dark:text-slate-300">
                                {{ $rep->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($rep->status === 'accepted')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/30 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-900/40">
                                        Aceite / Resolvido
                                    </span>
                                @elseif($rep->status === 'rejected')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 dark:bg-rose-950/30 dark:text-rose-455 border border-rose-100 dark:border-rose-900/40">
                                        Recusado
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 dark:bg-amber-950/30 dark:text-amber-400 border border-amber-100 dark:border-amber-900/40 animate-pulse">
                                        Aguardando
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-semibold">
                                @if($rep->status === 'pending')
                                    <div class="flex items-center justify-center gap-2">
                                        {{-- Accept Report Form --}}
                                        <form action="{{ route('admin.reports.handle', $rep) }}" method="post" class="inline" onsubmit="return confirm('Confirmar aceitação? Isto irá regenerar o recibo PDF e reenviar o email ao cliente.');">
                                            @csrf
                                            <input type="hidden" name="action" value="accept">
                                            <button type="submit" class="inline-flex items-center justify-center px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold transition shadow-sm cursor-pointer">
                                                Aceitar & Reenviar
                                            </button>
                                        </form>

                                        {{-- Reject Report Form --}}
                                        <form action="{{ route('admin.reports.handle', $rep) }}" method="post" class="inline" onsubmit="return confirm('Tem a certeza que deseja negar este pedido de reenvio de recibo?');">
                                            @csrf
                                            <input type="hidden" name="action" value="reject">
                                            <button type="submit" class="inline-flex items-center justify-center px-3 py-1.5 bg-rose-50 hover:bg-rose-100 border border-rose-250 text-rose-700 dark:bg-rose-950/20 dark:border-rose-900/60 dark:text-rose-400 dark:hover:bg-rose-950/45 rounded-lg text-xs font-bold transition shadow-sm cursor-pointer">
                                                Negar Pedido
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400 dark:text-slate-500 font-medium italic">Processado</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if(method_exists($reports, 'links'))
            <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/10 border-t border-slate-100 dark:border-slate-800/60 pagination-clean">
                {{ $reports->links() }}
            </div>
        @endif
    @endif
</div>
@endsection
