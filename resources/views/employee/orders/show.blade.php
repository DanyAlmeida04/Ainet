@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Encomenda #{{ $order->id }} (Funcionário)</h1>

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

    <form action="{{ route('employee.orders.close', $order) }}" method="post">
        @csrf
        <button class="btn btn-success">Fechar Encomenda</button>
    </form>
</div>
@endsection
