@extends('layouts.admin')

@section('admin-content')
<div class="bg-white dark:bg-slate-900 rounded-2xl shadow border border-slate-200/60 dark:border-slate-800 p-6">
    <h2 class="text-xl font-bold text-slate-800 dark:text-white mb-4">Encomenda #{{ $order->id }}</h2>

    <p>Cliente: {{ $order->customer_id }}</p>
    <p>Data: {{ $order->date->format('Y-m-d') }}</p>
    <p>Status: {{ $order->status }}</p>
    <p>Total: {{ number_format($order->total_price,2) }}</p>

    <h3>Items</h3>
    <ul>
        @foreach($order->items as $it)
            <li>{{ $it->qty }} x {{ $it->tshirtImage->name ?? 'Design' }} ({{ $it->size }}) - {{ number_format($it->unit_price,2) }}</li>
        @endforeach
    </ul>

    <div class="mt-4 flex gap-2">
        @if($order->receipt_url)
            <a href="{{ route('orders.receipt', $order) }}" class="btn btn-sm btn-primary">Ver Recibo (cliente)</a>
            <a href="{{ route('admin.orders.preview', $order) }}" class="btn btn-sm btn-secondary" target="_blank">Preview Recibo (Admin)</a>
            <form action="{{ route('admin.orders.generateSend', $order) }}" method="post">
                @csrf
                <button class="btn btn-sm btn-success">Gerar e Enviar (recriar)</button>
            </form>
        @else
            <form action="{{ route('admin.orders.generateSend', $order) }}" method="post" class="flex gap-2">
                @csrf
                <button class="btn btn-sm btn-success">Gerar e Enviar Recibo</button>
                <a href="{{ route('admin.orders.preview', $order) }}" class="btn btn-sm btn-secondary" target="_blank">Preview (gerar se necessário)</a>
            </form>
        @endif
    </div>

    <form action="{{ route('admin.orders.close', $order) }}" method="post" class="mt-4">
        @csrf
        <button class="btn btn-success">Fechar Encomenda</button>
    </form>

    <form action="{{ route('admin.orders.cancel', $order) }}" method="post" class="mt-2">
        @csrf
        <label>Razão (opcional)</label>
        <input name="reason">
        <button class="btn btn-warning">Anular</button>
    </form>
</div>
@endsection
