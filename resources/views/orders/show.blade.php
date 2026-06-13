@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-7xl animate-fade-in">
    
    {{-- Header / Breadcrumbs --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div class="flex items-center gap-3">
            <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Detalhes da Encomenda #{{ $order->id }}</h2>
            @if($order->status === 'closed')
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/30 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-900/40">
                    Fechada
                </span>
            @elseif($order->status === 'canceled')
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 dark:bg-rose-950/30 dark:text-rose-400 border border-rose-100 dark:border-rose-900/40">
                    Anulada
                </span>
            @else
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 dark:bg-amber-950/30 dark:text-amber-400 border border-amber-100 dark:border-amber-900/40 animate-pulse">
                    Pendente
                </span>
            @endif
        </div>
        <a href="{{ route('orders.index') }}" class="text-sm font-semibold text-slate-500 hover:text-blue-600 hover:underline dark:text-slate-400 dark:hover:text-blue-400 transition-colors">
            &larr; Voltar ao Histórico
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 flex items-center gap-3 p-4 bg-emerald-50 dark:bg-emerald-950/20 text-emerald-800 dark:text-emerald-300 rounded-xl border border-emerald-200/60 dark:border-emerald-900/60 shadow-sm">
            <svg class="w-5 h-5 flex-shrink-0 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="text-sm font-semibold">{{ session('success') }}</span>
        </div>
    @endif

    <div class="flex flex-col lg:flex-row gap-6">
        
        {{-- Main Column: Items & Summary --}}
        <div class="flex-1 space-y-6">
            
            {{-- Order Meta Info Card --}}
            <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200/60 dark:border-slate-800 p-6">
                <h3 class="text-base font-bold text-slate-800 dark:text-white mb-4">Resumo da Encomenda</h3>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <span class="text-xs font-semibold text-slate-400 block uppercase">Data do Pedido</span>
                        <span class="font-medium text-slate-700 dark:text-slate-350 block mt-0.5">{{ $order->date->format('d/m/Y H:i') }}</span>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-slate-400 block uppercase">Total Cobrado</span>
                        <span class="font-bold text-slate-850 dark:text-white text-base block mt-0.5">{{ number_format($order->total_price, 2) }} €</span>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-slate-400 block uppercase">Método Pagamento</span>
                        <span class="font-medium text-slate-700 dark:text-slate-350 block mt-0.5">{{ $order->payment_type }}</span>
                    </div>
                </div>
            </div>

            {{-- Items Table Card --}}
            <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200/60 dark:border-slate-800 overflow-hidden">
                <div class="p-6 border-b border-slate-200/60 dark:border-slate-800">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white">Artigos Adquiridos</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-450 mt-1">Lista de t-shirts configuradas e compradas nesta encomenda.</p>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-800/30 border-b border-slate-100 dark:border-slate-800/60">
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-24">Visualização</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Descrição / Nome</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-24">Tamanho</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-36">Cor Base</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-24">Qtd.</th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-28">Preço Unit.</th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-32">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                            @foreach($order->items as $it)
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/10 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="relative w-14 h-14 bg-slate-50 dark:bg-slate-850 rounded-xl border border-slate-200/60 dark:border-slate-700/60 flex items-center justify-center p-1 overflow-hidden">
                                            @if($it->color_code)
                                                <img src="{{ asset('storage/tshirt_base/' . $it->color_code . '.jpg') }}" class="w-full h-full object-contain pointer-events-none select-none">
                                            @endif
                                            @if($it->tshirtImage && $it->tshirtImage->image_url)
                                                <img src="{{ $it->tshirtImage->isPrivate() ? route('tshirt-images.private', ['filename' => $it->tshirtImage->image_url]) : asset('storage/tshirt_images/' . $it->tshirtImage->image_url) }}" class="absolute w-[36%] h-[36%] object-contain top-[28%] left-1/2 -translate-x-1/2 pointer-events-none select-none drop-shadow-sm opacity-95">
                                            @else
                                                <div class="absolute inset-0 flex items-center justify-center bg-slate-250/10 text-[8px] text-slate-455 font-bold">Custom</div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-semibold text-slate-800 dark:text-slate-200 leading-tight">
                                            {{ $it->tshirtImage->name ?? 'Imagem Personalizada' }}
                                        </div>
                                        <div class="text-xs text-slate-400 mt-0.5">
                                            Ref: {{ $it->tshirt_image_id ?? 'N/A' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center justify-center bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-300 font-semibold px-2 py-1 text-xs rounded-lg min-w-8">
                                            {{ $it->size }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-1.5 text-xs text-slate-700 dark:text-slate-300">
                                            <span class="w-3.5 h-3.5 rounded-full inline-block border border-slate-300 dark:border-slate-600 shadow-sm shrink-0" style="background-color: #{{ $it->color_code }}"></span>
                                            <span class="font-medium truncate max-w-[90px]">{{ $it->color->name ?? $it->color_code }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600 dark:text-slate-300 font-medium">
                                        {{ $it->qty }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium text-slate-600 dark:text-slate-300">
                                        {{ number_format($it->unit_price, 2) }} €
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold text-slate-800 dark:text-white">
                                        {{ number_format($it->sub_total, 2) }} €
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Notes Card --}}
            @if($order->notes)
                <div class="bg-slate-50 dark:bg-slate-900/40 rounded-2xl shadow-sm border border-slate-200/60 dark:border-slate-800 p-6">
                    <h3 class="text-sm font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Observações Adicionadas</h3>
                    <p class="text-sm text-slate-655 dark:text-slate-400 italic bg-white dark:bg-slate-900/50 p-4 rounded-xl border border-slate-100 dark:border-slate-800">
                        "{{ $order->notes }}"
                    </p>
                </div>
            @endif

            {{-- Canceled reason card --}}
            @if($order->status === 'canceled')
                <div class="bg-rose-50 dark:bg-rose-950/20 border border-rose-200 dark:border-rose-900/40 rounded-2xl p-6">
                    <h3 class="text-sm font-bold text-rose-800 dark:text-rose-300 uppercase tracking-wider mb-2">Encomenda Cancelada</h3>
                    <p class="text-sm text-rose-700 dark:text-rose-400 italic">
                        Motivo: {{ $order->reason_for_cancellation ?: 'Sem justificação específica registada.' }}
                    </p>
                </div>
            @endif
        </div>

        {{-- Sidebar Column: Details & Receipts --}}
        <div class="w-full lg:w-96 space-y-6">
            
            {{-- Billing & Shipping info --}}
            <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200/60 dark:border-slate-800 p-6">
                <h3 class="text-base font-bold text-slate-800 dark:text-white mb-4">Informação de Envio</h3>
                
                <div class="space-y-4 text-sm">
                    <div>
                        <span class="text-xs font-semibold text-slate-400 block">NIF para Faturação</span>
                        <span class="font-medium text-slate-700 dark:text-slate-300">{{ $order->nif ?: 'Não fornecido' }}</span>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-slate-400 block">Morada de Entrega</span>
                        <span class="font-medium text-slate-700 dark:text-slate-300 leading-normal block">{{ $order->address }}</span>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-slate-400 block">Referência de Pagamento</span>
                        <span class="font-mono text-xs text-slate-700 dark:text-slate-300 block bg-slate-50 dark:bg-slate-850 p-2 rounded-lg border border-slate-100 dark:border-slate-800/80 mt-1 select-all">
                            {{ $order->payment_ref }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Receipt Actions --}}
            <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200/60 dark:border-slate-800 p-6">
                <h3 class="text-base font-bold text-slate-800 dark:text-white mb-3">Recibo</h3>
                <p class="text-xs text-slate-500 dark:text-slate-455 mb-5">
                    O recibo PDF oficial é emitido automaticamente assim que a encomenda é expedida.
                </p>

                @if($order->receipt_url)
                    <div class="bg-emerald-50/30 dark:bg-emerald-950/10 border border-emerald-200/50 dark:border-emerald-900/30 rounded-xl p-3.5 mb-5 space-y-2">
                        <div class="flex items-center gap-2 text-xs font-semibold text-emerald-800 dark:text-emerald-400">
                            <svg class="w-4.5 h-4.5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Recibo disponível</span>
                        </div>
                    </div>

                    <div class="space-y-2.5">
                        <a href="{{ route('orders.receipt', $order) }}" 
                           class="w-full flex items-center justify-center gap-1.5 bg-blue-600 text-white font-semibold py-2.5 px-4 rounded-xl text-sm hover:bg-blue-700 hover:shadow transition shadow-sm cursor-pointer text-center">
                            <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Descarregar PDF
                        </a>

                        <a href="{{ route('orders.preview', $order) }}" target="_blank"
                           class="w-full flex items-center justify-center gap-1.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 font-semibold py-2.5 px-4 rounded-xl text-sm hover:bg-slate-200 dark:hover:bg-slate-700 transition cursor-pointer text-center">
                            Visualizar no Navegador
                        </a>

                        <form action="{{ route('orders.resendReceipt', $order) }}" method="POST" class="pt-2">
                            @csrf
                            <button type="submit" class="w-full flex items-center justify-center gap-1.5 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800/50 text-slate-750 dark:text-slate-300 font-semibold py-2.5 px-4 rounded-xl text-sm transition cursor-pointer">
                                Reenviar Recibo por Email
                            </button>
                        </form>
                    </div>
                @else
                    <div class="bg-amber-55/10 border border-amber-200/60 dark:border-amber-900/60 rounded-xl p-3.5 mb-5">
                        <span class="text-xs font-semibold text-amber-700 dark:text-amber-400 block">Recibo não gerado</span>
                        <p class="text-[11px] text-amber-600 dark:text-amber-500 mt-0.5 leading-normal">
                            Esta encomenda ainda não foi expedida ou faturada. A geração ocorrerá de forma automática.
                        </p>
                    </div>

                    <div class="space-y-2.5">
                        <a href="{{ route('orders.preview', $order) }}" target="_blank"
                           class="w-full flex items-center justify-center gap-1.5 bg-slate-100 dark:bg-slate-800 text-slate-750 dark:text-slate-200 font-semibold py-2.5 px-4 rounded-xl text-sm hover:bg-slate-200 dark:hover:bg-slate-700 transition cursor-pointer text-center">
                            Gerar & Preview no Navegador
                        </a>

                        <form action="{{ route('orders.resendReceipt', $order) }}" method="POST" class="pt-2">
                            @csrf
                            <button type="submit" class="w-full flex items-center justify-center gap-1.5 bg-blue-600 text-white font-semibold py-2.5 px-4 rounded-xl text-sm hover:bg-blue-700 transition cursor-pointer">
                                Gerar & Enviar Recibo por Email
                            </button>
                        </form>
                    </div>
                @endif
            </div>

        </div>

    </div>
</div>
@endsection
