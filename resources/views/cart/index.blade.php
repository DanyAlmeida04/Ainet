@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">Carrinho de Compras</h1>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4">{{ session('success') }}</div>
    @endif

    @php $total = 0; @endphp

    @if(empty($cart) || count($cart) == 0)
        <div class="bg-yellow-100 p-4 rounded">O seu carrinho está vazio.</div>
    @else
        <table class="w-full bg-white rounded shadow">
            <thead>
                <tr class="border-b">
                    <th class="p-3 text-left">Produto</th>
                    <th class="p-3">Cor</th>
                    <th class="p-3">Tamanho</th>
                    <th class="p-3">Quantidade</th>
                    <th class="p-3">Unit</th>
                    <th class="p-3">Subtotal</th>
                    <th class="p-3">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cart as $key => $item)
                    @php
                        // calcular preço unitario com base em Price::current() simplificado
                        $isOwn = false; // por agora assumimos catálogo
                        $unit = $priceConf ? $priceConf->unit_price_catalog : 10.00;
                        $sub = $unit * $item['qty'];
                        $total += $sub;
                    @endphp
                    <tr class="border-b">
                        <td class="p-3 flex items-center gap-3">
                            <img src="{{ asset('storage/tshirt_images/' . $item['image_url']) }}" alt="{{ $item['name'] }}" class="w-20 h-20 object-contain">
                            <div>
                                <div class="font-semibold">{{ $item['name'] }}</div>
                            </div>
                        </td>
                        <td class="p-3 text-center">{{ $item['color_code'] }}</td>
                        <td class="p-3 text-center">{{ $item['size'] }}</td>
                        <td class="p-3 text-center">
                            <form action="{{ route('cart.update') }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="key" value="{{ $key }}">
                                <input type="number" name="qty" value="{{ $item['qty'] }}" min="0" class="w-20 text-center border rounded px-2 py-1">
                                <button class="ml-2 bg-blue-600 text-white px-3 py-1 rounded">OK</button>
                            </form>
                        </td>
                        <td class="p-3 text-right">€{{ number_format($unit, 2) }}</td>
                        <td class="p-3 text-right">€{{ number_format($sub, 2) }}</td>
                        <td class="p-3 text-center">
                            <form action="{{ route('cart.remove') }}" method="POST">
                                @csrf
                                <input type="hidden" name="key" value="{{ $key }}">
                                <button class="bg-red-500 text-white px-3 py-1 rounded">Remover</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4 text-right">
            <div class="text-lg font-semibold">Total: €{{ number_format($total, 2) }}</div>
            <div class="mt-4 flex justify-end gap-2">
                <form action="{{ route('cart.clear') }}" method="POST">
                    @csrf
                    <button class="bg-gray-500 text-white px-4 py-2 rounded">Limpar Carrinho</button>
                </form>

                <a href="{{ route('checkout') }}" class="bg-green-600 text-white px-4 py-2 rounded">Finalizar Compra</a>
            </div>
        </div>
    @endif
</div>
@endsection
