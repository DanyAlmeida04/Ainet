@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Encomendas Pendentes (Funcionário)</h1>

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Cliente</th>
                <th>Data</th>
                <th>Total</th>
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
                <td>
                    <a href="{{ route('employee.orders.show', $o) }}" class="btn btn-sm btn-primary">Ver</a>
                    <form action="{{ route('employee.orders.close', $o) }}" method="post" style="display:inline">
                        @csrf
                        <button class="btn btn-sm btn-success">Marcar como Fechada</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $orders->links() }}
</div>
@endsection
