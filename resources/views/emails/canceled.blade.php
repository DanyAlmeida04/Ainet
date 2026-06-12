@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-xl">
    <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200/60 dark:border-slate-800 shadow-sm">
        <h2 class="text-xl font-bold mb-3 text-rose-600 dark:text-rose-405">A sua encomenda foi anulada</h2>
        <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">Olá <strong>{{ $order->customer->user->name ?? 'Cliente' }}</strong>,</p>
        <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">Informamos que a sua encomenda <strong>#{{ $order->id }}</strong> foi anulada e o seu estado foi atualizado para cancelado.</p>
        
        @if($order->reason_for_cancellation)
            <div class="bg-rose-50 dark:bg-rose-950/20 p-4 rounded-xl border border-rose-150 dark:border-rose-900/60 mb-4">
                <span class="text-xs font-bold text-rose-800 dark:text-rose-400 block mb-1">Motivo da Anulação:</span>
                <p class="text-xs text-rose-700 dark:text-rose-350 italic">"{{ $order->reason_for_cancellation }}"</p>
            </div>
        @endif
        
        <p class="text-sm text-slate-600 dark:text-slate-400">Se tiver alguma dúvida ou pretender informações adicionais sobre este processo, por favor contacte a nossa equipa.</p>
        <p class="mt-6 text-sm text-slate-500 dark:text-slate-450 border-t border-slate-100 dark:border-slate-800 pt-4">Cumprimentos,<br><strong>Equipa FunShirt</strong></p>
    </div>
</div>
@endsection
