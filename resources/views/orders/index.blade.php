@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">As Minhas Encomendas</h1>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4">{{ session('success') }}</div>
    @endif

    @if($orders->count() == 0)
        <div class="bg-yellow-100 p-4 rounded">Ainda não tem encomendas.</div>
    @else
        <div class="space-y-4">
            @foreach($orders as $order)
                <div class="bg-white p-4 rounded shadow flex items-center justify-between">
                    <div>
                        <div class="font-semibold">Encomenda #{{ $order->id }} - {{ $order->status }}</div>
                        <div class="text-sm text-gray-600">Data: {{ $order->date->format('Y-m-d') }} - Total: €{{ number_format($order->total_price, 2) }}</div>
                    </div>
                    <div class="flex gap-2">
                        @if($order->receipt_url)
                            <a href="{{ route('orders.receipt', $order) }}" target="_blank" class="bg-blue-600 text-white px-3 py-1 rounded">Ver Recibo</a>
                            <form action="{{ route('orders.resendReceipt', $order) }}" method="POST">
                                @csrf
                                <button class="bg-gray-500 text-white px-3 py-1 rounded">Reenviar Recibo</button>
                            </form>
                        @else
                            <span class="text-sm text-gray-500">Recibo indisponível</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">{{ $orders->links() }}</div>
    @endif
</div>
@endsection
