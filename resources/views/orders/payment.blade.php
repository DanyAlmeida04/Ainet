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
                    $isOwn = $item['is_private'] ?? false;
                    $unit = 10.00;
                    if ($priceConf) {
                        $threshold = $priceConf->qty_discount ?? 0;
                        if ($isOwn) {
                            if ($threshold > 0 && $item['qty'] >= $threshold) {
                                $unit = $priceConf->unit_price_own_discount;
                            } else {
                                $unit = $priceConf->unit_price_own;
                            }
                        } else {
                            if ($threshold > 0 && $item['qty'] >= $threshold) {
                                $unit = $priceConf->unit_price_catalog_discount;
                            } else {
                                $unit = $priceConf->unit_price_catalog;
                            }
                        }
                    }
                    $sub = $unit * $item['qty'];
                    $total += $sub;
                    $colorObj = \App\Models\Color::find($item['color_code']);
                @endphp
                <div class="flex items-center gap-3 border-b py-3">
                    <div class="relative w-14 h-14 bg-slate-50 dark:bg-slate-900 rounded border border-slate-200/50 dark:border-slate-800 flex items-center justify-center p-1 overflow-hidden shrink-0">
                        {{-- Base T-shirt --}}
                        <img src="{{ asset('storage/tshirt_base/' . $item['color_code'] . '.jpg') }}" class="w-full h-full object-contain pointer-events-none select-none">
                        {{-- Design Overlay --}}
                        <img src="{{ ($item['is_private'] ?? false) ? route('tshirt-images.private', ['filename' => $item['image_url']]) : asset('storage/tshirt_images/' . $item['image_url']) }}" class="absolute w-[36%] h-[36%] object-contain top-[28%] left-1/2 -translate-x-1/2 pointer-events-none select-none drop-shadow-sm opacity-90">
                    </div>
                    <div class="flex-1">
                        <div class="font-semibold text-slate-900 dark:text-white">{{ $item['name'] }}</div>
                        <div class="text-xs text-slate-500 dark:text-slate-400 flex flex-wrap items-center gap-x-2">
                            <span>Tam: <strong>{{ $item['size'] }}</strong></span>
                            <span>|</span>
                            <span>Qty: <strong>{{ $item['qty'] }}</strong></span>
                            <span>|</span>
                            <span class="inline-flex items-center gap-1">
                                <span class="w-2.5 h-2.5 rounded-full inline-block border border-slate-300 dark:border-slate-600" style="background-color: #{{ $item['color_code'] }}"></span>
                                {{ $colorObj?->name ?? $item['color_code'] }}
                            </span>
                        </div>
                    </div>
                    <div class="text-right text-sm font-semibold">€{{ number_format($sub, 2) }}</div>
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
