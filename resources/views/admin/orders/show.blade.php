@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Encomenda #{{ $order->id }}</h1>

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

    @if($order->receipt_url)
        <a href="{{ route('orders.receipt', $order) }}" class="btn btn-sm btn-primary">Ver Recibo (cliente)</a>
    @endif

    <form action="{{ route('admin.orders.close', $order) }}" method="post">
        @csrf
        <button class="btn btn-success">Fechar Encomenda</button>
    </form>

    <form action="{{ route('admin.orders.cancel', $order) }}" method="post">
        @csrf
        <label>Razão (opcional)</label>
        <input name="reason">
        <button class="btn btn-warning">Anular</button>
    </form>
</div>
@endsection
