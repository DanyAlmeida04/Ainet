@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">Finalizar Compra</h1>

    @if($errors->any())
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white p-4 rounded shadow">
            <h2 class="font-semibold mb-3">Resumo da Encomenda</h2>
            @php $total = 0; @endphp
            @forelse($cart as $key => $item)
                @php
                    $unit = $priceConf ? $priceConf->unit_price_catalog : 10.00;
                    $sub = $unit * $item['qty'];
                    $total += $sub;
                @endphp
                <div class="flex items-center gap-3 border-b py-3">
                    <img src="{{ asset('storage/tshirt_images/' . $item['image_url']) }}" class="w-16 h-16 object-contain">
                    <div class="flex-1">
                        <div class="font-semibold">{{ $item['name'] }}</div>
                        <div class="text-sm text-gray-600">{{ $item['color_code'] }} | {{ $item['size'] }}</div>
                    </div>
                    <div class="text-right">€{{ number_format($sub, 2) }}</div>
                </div>
            @empty
                <p>O seu carrinho está vazio.</p>
            @endforelse

            <div class="mt-4 text-right font-semibold">Total: €{{ number_format($total, 2) }}</div>
        </div>

        <div class="bg-white p-4 rounded shadow">
            <h2 class="font-semibold mb-3">Dados de Pagamento e Entrega</h2>
            <form action="{{ route('cart.processPayment') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="block text-sm">NIF</label>
                    <input type="text" name="nif" value="{{ old('nif', $customer->nif ?? '') }}" class="w-full border rounded px-2 py-2">
                </div>
                <div class="mb-3">
                    <label class="block text-sm">Morada</label>
                    <input type="text" name="address" value="{{ old('address', $customer->address ?? '') }}" class="w-full border rounded px-2 py-2">
                </div>

                <div class="mb-3">
                    <label class="block text-sm">Método de Pagamento</label>
                    <select name="payment_type" class="w-full border rounded px-2 py-2">
                        <option value="Visa">Visa</option>
                        <option value="PayPal">PayPal</option>
                        <option value="MB WAY">MB WAY</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="block text-sm">Referência de Pagamento</label>
                    <input type="text" name="payment_ref" value="{{ old('payment_ref', $customer->default_payment_ref ?? '') }}" class="w-full border rounded px-2 py-2">
                </div>

                <button class="w-full bg-green-600 text-white px-4 py-2 rounded">Pagar e Criar Encomenda</button>
            </form>
        </div>
    </div>
</div>
@endsection
