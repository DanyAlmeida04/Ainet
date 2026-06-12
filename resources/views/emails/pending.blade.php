@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-xl">
    <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200/60 dark:border-slate-800 shadow-sm">
        <h2 class="text-xl font-bold mb-3 text-slate-800 dark:text-white">A sua encomenda está em processamento!</h2>
        <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">Olá <strong>{{ $order->customer->user->name ?? 'Cliente' }}</strong>,</p>
        <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">Confirmamos que a sua encomenda <strong>#{{ $order->id }}</strong> foi registada com sucesso no estado <strong>pendente</strong> e encontra-se agora na fila de estampagem e expedição.</p>
        
        <div class="bg-slate-50 dark:bg-slate-800/40 p-4 rounded-xl border border-slate-100 dark:border-slate-850 mb-4">
            <span class="text-xs font-semibold text-slate-400 block mb-1 uppercase tracking-wider">Detalhes da Encomenda</span>
            <ul class="text-xs text-slate-600 dark:text-slate-300 list-disc list-inside space-y-1">
                <li>Número: #{{ $order->id }}</li>
                <li>Morada: {{ $order->address }}</li>
                <li>Total: {{ number_format($order->total_price, 2) }} €</li>
            </ul>
        </div>
        
        <p class="text-sm text-slate-600 dark:text-slate-400">Receberá um novo e-mail com o recibo PDF em anexo assim que a sua encomenda for expedida.</p>
        <p class="mt-6 text-sm text-slate-500 dark:text-slate-450 border-t border-slate-100 dark:border-slate-800 pt-4">Cumprimentos,<br><strong>Equipa FunShirt</strong></p>
    </div>
</div>
@endsection
