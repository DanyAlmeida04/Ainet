@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Encomendas (Admin)</h1>

    <form method="get" class="mb-4">
        <input type="text" name="customer_id" placeholder="Customer ID" value="{{ request('customer_id') }}">
        <select name="status">
            <option value="">Todos</option>
            <option value="pending" {{ request('status')=='pending'?'selected':'' }}>Pending</option>
            <option value="closed" {{ request('status')=='closed'?'selected':'' }}>Closed</option>
            <option value="canceled" {{ request('status')=='canceled'?'selected':'' }}>Canceled</option>
        </select>
        <button class="btn btn-sm btn-primary">Filtrar</button>
    </form>

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Cliente</th>
                <th>Data</th>
                <th>Total</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $o)
            <tr>
                <td>{{ $o->id }}</td>
                <td>{{ $o->customer_id }}</td>
                <td>{{ $o->date->format('Y-m-d') }}</td>
                <td>{{ number_format($o->total_price,2) }}</td>
                <td>{{ $o->status }}</td>
                <td>
                    <a href="{{ route('admin.orders.show', $o) }}" class="btn btn-sm btn-primary">Ver</a>
                    <form action="{{ route('admin.orders.close', $o) }}" method="post" style="display:inline">
                        @csrf
                        <button class="btn btn-sm btn-success">Fechar</button>
                    </form>
                    <form action="{{ route('admin.orders.cancel', $o) }}" method="post" style="display:inline">
                        @csrf
                        <button class="btn btn-sm btn-warning">Anular</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $orders->links() }}
</div>
@endsection
