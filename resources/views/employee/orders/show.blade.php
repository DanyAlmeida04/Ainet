@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div class="flex items-center gap-3">
            <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Processamento Encomenda #{{ $order->id }}</h2>
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 dark:bg-amber-950/30 dark:text-amber-400 border border-amber-100 dark:border-amber-900/40 animate-pulse">
                Pendente
            </span>
        </div>
        <a href="{{ route('employee.orders.index') }}" class="text-sm font-semibold text-slate-500 hover:text-blue-600 hover:underline dark:text-slate-400 dark:hover:text-blue-400 transition-colors">
            &larr; Voltar à Fila
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        {{-- Shipping & Customer Details Card --}}
        <div class="md:col-span-2 bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200/60 dark:border-slate-800 p-6 space-y-4">
            <h3 class="text-base font-bold text-slate-800 dark:text-white border-b border-slate-100 dark:border-slate-850 pb-2">Destinatário & Envio</h3>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-xs font-semibold text-slate-400 block uppercase">Nome do Cliente</span>
                    <span class="font-semibold text-slate-700 dark:text-slate-200 block mt-0.5">{{ $order->customer->user->name ?? 'Cliente #'.$order->customer_id }}</span>
                    <span class="text-xs text-slate-500 dark:text-slate-400 block">{{ $order->customer->user->email ?? '' }}</span>
                </div>
                <div>
                    <span class="text-xs font-semibold text-slate-400 block uppercase">NIF para Faturação</span>
                    <span class="font-medium text-slate-700 dark:text-slate-200 block mt-0.5">{{ $order->nif ?: '—' }}</span>
                </div>
                <div class="sm:col-span-2">
                    <span class="text-xs font-semibold text-slate-400 block uppercase">Morada de Entrega</span>
                    <span class="font-medium text-slate-700 dark:text-slate-200 block mt-0.5 leading-relaxed bg-slate-50 dark:bg-slate-800/40 p-2.5 rounded-xl border border-slate-100 dark:border-slate-800">{{ $order->address }}</span>
                </div>
            </div>

            @if($order->notes)
                <div class="mt-4 bg-slate-50 dark:bg-slate-800/30 p-4 rounded-xl border border-slate-200/60 dark:border-slate-800">
                    <span class="text-xs font-semibold text-slate-400 block uppercase mb-1">Notas da Encomenda</span>
                    <p class="text-xs text-slate-600 dark:text-slate-350 italic">"{{ $order->notes }}"</p>
                </div>
            @endif
        </div>

        {{-- Actions Card --}}
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200/60 dark:border-slate-800 p-6 flex flex-col justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-800 dark:text-white border-b border-slate-100 dark:border-slate-850 pb-2">Controlo Logístico</h3>
                <p class="text-xs text-slate-500 dark:text-slate-450 mt-3 leading-relaxed">
                    Certifique-se de que todas as estampas foram aplicadas corretamente e as t-shirts foram empacotadas antes de expedir.
                </p>
            </div>

            <div class="mt-6">
                <form action="{{ route('employee.orders.close', $order) }}" method="post">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2.5 px-4 rounded-xl text-sm transition shadow-sm cursor-pointer">
                        <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        Concluir & Enviar Recibo
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Items Table Card --}}
    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200/60 dark:border-slate-800 overflow-hidden">
        <div class="p-6 border-b border-slate-200/60 dark:border-slate-800">
            <h3 class="text-lg font-bold text-slate-800 dark:text-white">Artigos a Estampar</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Lista de designs, cores, tamanhos e quantidades para esta encomenda.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/30 border-b border-slate-100 dark:border-slate-800/60">
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-24">Visualização</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Design / Estampa</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-24">Tamanho</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-36">Cor Base</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-24">Qtd.</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                    @foreach($order->items as $it)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/10 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="relative w-14 h-14 bg-slate-50 dark:bg-slate-800 rounded-xl border border-slate-200/60 dark:border-slate-700/60 flex items-center justify-center p-1 overflow-hidden">
                                    @if($it->color_code)
                                        <img src="{{ asset('storage/tshirt_base/' . $it->color_code . '.jpg') }}" class="w-full h-full object-contain pointer-events-none select-none">
                                    @endif
                                    @if($it->tshirtImage && $it->tshirtImage->image_url)
                                        <img src="{{ $it->tshirtImage->isPrivate() ? route('tshirt-images.private', ['filename' => $it->tshirtImage->image_url]) : asset('storage/tshirt_images/' . $it->tshirtImage->image_url) }}" class="absolute w-[36%] h-[36%] object-contain top-[28%] left-1/2 -translate-x-1/2 pointer-events-none select-none drop-shadow-sm opacity-95">
                                    @else
                                        <div class="absolute inset-0 flex items-center justify-center bg-slate-200/10 text-[8px] text-slate-400 font-bold">Custom</div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-slate-800 dark:text-slate-200 leading-tight">
                                    {{ $it->tshirtImage->name ?? 'Imagem Personalizada' }}
                                </div>
                                <div class="text-xs text-slate-400 mt-0.5">
                                    Ref: {{ $it->tshirt_image_id ?? 'N/A' }} {{ $it->tshirtImage && $it->tshirtImage->isPrivate() ? '(Imagem Privada)' : '' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center justify-center bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-300 font-semibold px-2.5 py-1 text-xs rounded-lg min-w-8">
                                    {{ $it->size }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-1.5 text-xs text-slate-700 dark:text-slate-300">
                                    <span class="w-3.5 h-3.5 rounded-full inline-block border border-slate-300 dark:border-slate-600 shadow-sm shrink-0" style="background-color: #{{ $it->color_code }}"></span>
                                    <span class="font-medium">{{ $it->color->name ?? $it->color_code }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-850 dark:text-slate-150 font-bold">
                                {{ $it->qty }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
