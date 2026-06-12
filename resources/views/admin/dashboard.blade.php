@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Painel Admin</h1>

    <div class="grid grid-cols-3 gap-4 my-6">
        <div class="p-4 bg-white rounded shadow">
            <h3 class="font-bold">Total Vendas</h3>
            <div class="text-2xl">€ {{ number_format($totalSales,2) }}</div>
        </div>
        <div class="p-4 bg-white rounded shadow">
            <h3 class="font-bold">Total Encomendas</h3>
            <div class="text-2xl">{{ $totalOrders }}</div>
        </div>
        <div class="p-4 bg-white rounded shadow">
            <h3 class="font-bold">Encomendas por Estado</h3>
            <ul>
                @foreach(['pending','closed','canceled'] as $s)
                    <li>{{ $s }}: {{ $ordersByStatus[$s] ?? 0 }}</li>
                @endforeach
            </ul>
        </div>
    </div>

    <h2 class="mt-6">Encomendas Recentes</h2>
    <table class="table w-full bg-white">
        <thead>
            <tr>
                <th>ID</th>
                <th>Cliente</th>
                <th>Data</th>
                <th>Total</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($recentOrders as $o)
            <tr>
                <td>{{ $o->id }}</td>
                <td>{{ $o->customer_id }}</td>
                <td>{{ $o->date->format('Y-m-d') }}</td>
                <td>€ {{ number_format($o->total_price,2) }}</td>
                <td>{{ $o->status }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <h2 class="mt-6">Top T-Shirts</h2>
    <ul class="bg-white p-4 rounded shadow">
        @foreach($topTshirts as $t)
            <li class="mb-2 flex items-center">
                @if($t['image'])
                    <img src="{{ asset('storage/tshirt_images/' . ($t['image']->image_url ?? 'default.png')) }}" class="w-12 h-12 mr-3" alt="">
                    <div>{{ $t['image']->name ?? 'Design #' . ($t['image']->id ?? '') }}</div>
                @else
                    <div>Design #{{ $t['image']->id ?? '' }}</div>
                @endif
                <div class="ml-auto">Vendidas: {{ $t['qty'] }}</div>
            </li>
        @endforeach
    </ul>
</div>
@endsection
