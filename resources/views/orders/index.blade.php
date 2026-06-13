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

    @if($errors->any())
        <div class="mb-6 p-4 bg-rose-50 dark:bg-rose-950/20 text-rose-800 dark:text-rose-300 rounded-xl border border-rose-200/60 dark:border-rose-900/60 shadow-sm">
            <ul class="list-disc list-inside text-xs space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200/60 dark:border-slate-800 overflow-hidden mb-6">
        <div class="p-6 border-b border-slate-200/60 dark:border-slate-800">
            <h2 class="text-2xl font-bold text-slate-800 dark:text-white">As Minhas Encomendas</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Acompanhe o estado das suas encomendas e aceda aos seus recibos.</p>
        </div>

        @if($orders->count() == 0)
            <div class="p-12 text-center text-slate-500 dark:text-slate-400">
                <svg class="w-12 h-12 mx-auto text-slate-300 dark:text-slate-650 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                <p class="text-base font-semibold">Ainda não tem encomendas.</p>
                <p class="text-xs text-slate-400 mt-1">Visite o nosso catálogo para configurar e comprar as suas t-shirts!</p>
                <a href="{{ route('catalog.index') }}" class="inline-flex items-center gap-1.5 bg-blue-600 text-white font-semibold py-2 px-4 rounded-xl text-xs hover:bg-blue-700 transition mt-4 shadow-sm">
                    Ir para o Catálogo
                </a>
            </div>
        @else
            {{-- Orders Table --}}
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-800/30 border-b border-slate-100 dark:border-slate-800/60">
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-24">ID</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-36">Data</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Artigos</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-32">Total</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-36">Estado</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-56">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                        @foreach($orders as $order)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/10 transition-colors duration-100">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-600 dark:text-slate-355">
                                    #{{ $order->id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600 dark:text-slate-300">
                                    {{ $order->date->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4">
                                    {{-- List order items visual thumbnails --}}
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        @foreach($order->items->take(4) as $it)
                                            <div class="relative w-9 h-9 bg-slate-50 dark:bg-slate-850 rounded-lg border border-slate-200/60 dark:border-slate-700/60 flex items-center justify-center p-0.5 overflow-hidden shadow-sm" title="{{ $it->tshirtImage->name ?? 'Imagem Personalizada' }} (Tam: {{ $it->size }}, Qtd: {{ $it->qty }})">
                                                @if($it->color_code)
                                                    <img src="{{ asset('storage/tshirt_base/' . $it->color_code . '.jpg') }}" class="w-full h-full object-contain pointer-events-none select-none">
                                                @endif
                                                @if($it->tshirtImage && $it->tshirtImage->image_url)
                                                    <img src="{{ $it->tshirtImage->isPrivate() ? route('tshirt-images.private', ['filename' => $it->tshirtImage->image_url]) : asset('storage/tshirt_images/' . $it->tshirtImage->image_url) }}" class="absolute w-[36%] h-[36%] object-contain top-[28%] left-1/2 -translate-x-1/2 pointer-events-none select-none drop-shadow-sm opacity-95">
                                                @else
                                                    <div class="absolute inset-0 flex items-center justify-center bg-slate-250/10 text-[6px] text-slate-400 font-bold">Custom</div>
                                                @endif
                                            </div>
                                        @endforeach
                                        @if($order->items->count() > 4)
                                            <span class="text-xs text-slate-400 font-semibold pl-1">+{{ $order->items->count() - 4 }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-800 dark:text-white">
                                    {{ number_format($order->total_price, 2) }} €
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($order->status === 'closed')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/30 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-900/40">
                                            Fechada
                                        </span>
                                    @elseif($order->status === 'canceled')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 dark:bg-rose-950/30 dark:text-rose-400 border border-rose-100 dark:border-rose-900/40">
                                            Anulada
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 dark:bg-amber-950/30 dark:text-amber-400 border border-amber-100 dark:border-amber-900/40 animate-pulse">
                                            Pendente
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-semibold">
                                    <div class="flex items-center justify-center gap-1.5">
                                        {{-- View details eye icon --}}
                                        <a href="{{ route('orders.show', $order) }}" class="inline-flex items-center justify-center p-2 rounded-lg text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-blue-600 dark:hover:text-blue-400 transition" title="Ver Detalhes da Encomenda">
                                            <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>

                                        @if($order->receipt_url)
                                            {{-- PDF Receipt Download --}}
                                            <a href="{{ route('orders.receipt', $order) }}" target="_blank" class="inline-flex items-center justify-center p-2 rounded-lg text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-emerald-600 dark:hover:text-emerald-400 transition" title="Descarregar Recibo PDF">
                                                <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                </svg>
                                            </a>

                                            {{-- Preview Receipt --}}
                                            <a href="{{ route('orders.preview', $order) }}" target="_blank" class="inline-flex items-center justify-center p-2 rounded-lg text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-indigo-600 dark:hover:text-indigo-400 transition" title="Visualizar Recibo no Navegador">
                                                <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                            </a>
                                        @endif

                                        {{-- Receipt Issue Report Controls --}}
                                        @if($order->status === 'closed')
                                            @php
                                                $reps = $order->receiptReports;
                                                $repCount = $reps->count();
                                                $latestRep = $reps->sortByDesc('created_at')->first();
                                            @endphp

                                            @if($repCount < 3 && (!$latestRep || $latestRep->status !== 'pending'))
                                                <form action="{{ route('orders.reportReceipt', $order) }}" method="POST" class="inline" onsubmit="return confirm('Tem a certeza que deseja reportar um problema com o recibo da encomenda #{{ $order->id }}?');">
                                                    @csrf
                                                    <button type="submit" class="inline-flex items-center justify-center p-2 rounded-lg text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-rose-600 dark:hover:text-rose-400 transition cursor-pointer" title="Reportar Problema com Recibo ({{ $repCount }}/3)">
                                                        <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            @endif

                                            @if($latestRep)
                                                @if($latestRep->status === 'pending')
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700 dark:bg-amber-950/20 dark:text-amber-400 border border-amber-100 dark:border-amber-900/40" title="Problema reportado ({{ $repCount }}/3). A aguardar análise.">Reportado (Pendente)</span>
                                                @elseif($latestRep->status === 'accepted')
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/20 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-900/40" title="Resolvido ({{ $repCount }}/3). Recibo gerado.">Resolvido</span>
                                                @elseif($latestRep->status === 'rejected')
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-rose-50 text-rose-700 dark:bg-rose-950/20 dark:text-rose-400 border border-rose-100 dark:border-rose-900/40" title="Recusado ({{ $repCount }}/3).">Recusado</span>
                                                @endif
                                            @endif

                                            @if($repCount >= 3)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-400 border border-slate-200 dark:border-slate-700" title="Atingiu o limite de 3 reportes para esta encomenda.">Limite de Reportes</span>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if(method_exists($orders, 'links'))
                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/10 border-t border-slate-100 dark:border-slate-800/60 pagination-clean">
                    {{ $orders->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
