@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">Carrinho de Compras</h1>

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
                    @endphp
                    <tr class="border-b">
                        <td class="p-3 flex items-center gap-3">
                            <div class="relative w-16 h-16 bg-slate-50 dark:bg-slate-900 rounded border border-slate-200/50 dark:border-slate-800 flex items-center justify-center p-1 overflow-hidden shrink-0">
                                {{-- Base T-shirt --}}
                                <img src="{{ asset('storage/tshirt_base/' . $item['color_code'] . '.jpg') }}" class="w-full h-full object-contain pointer-events-none select-none">
                                {{-- Design Overlay --}}
                                <img src="{{ ($item['is_private'] ?? false) ? route('tshirt-images.private', ['filename' => $item['image_url']]) : asset('storage/tshirt_images/' . $item['image_url']) }}" class="absolute w-[36%] h-[36%] object-contain top-[28%] left-1/2 -translate-x-1/2 pointer-events-none select-none drop-shadow-sm opacity-90">
                            </div>
                            <div>
                                <div class="font-semibold text-slate-900 dark:text-white">{{ $item['name'] }}</div>
                            </div>
                        </td>
                        <td class="p-3">
                            @php $colorObj = \App\Models\Color::find($item['color_code']); @endphp
                            <div class="flex items-center justify-center gap-1.5">
                                <span class="w-4.5 h-4.5 rounded-full border border-slate-300 dark:border-slate-600 inline-block shadow-sm shrink-0" style="background-color: #{{ $item['color_code'] }}"></span>
                                <span class="text-sm font-medium">{{ $colorObj?->name ?? $item['color_code'] }}</span>
                            </div>
                        </td>
                        <td class="p-3 text-center font-bold">{{ $item['size'] }}</td>

                        <td class="p-3 text-center">
                            <form action="{{ route('cart.update') }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="key" value="{{ $key }}">
                                <input type="number" name="qty" value="{{ $item['qty'] }}" min="0" class="w-20 text-center border rounded px-2 py-1">
                                <button class="ml-2 bg-blue-600 text-white px-3 py-1 rounded cursor-pointer transition hover:underline">OK</button>
                            </form>
                        </td>
                        <td class="p-3 text-right">€{{ number_format($unit, 2) }}</td>
                        <td class="p-3 text-right">€{{ number_format($sub, 2) }}</td>
                        <td class="p-3 text-center">
                            <form action="{{ route('cart.remove') }}" method="POST">
                                @csrf
                                <input type="hidden" name="key" value="{{ $key }}">
                                <button class="bg-red-500 text-white px-3 py-1 rounded cursor-pointer transition hover:underline">Remover</button>
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
                    <button class="bg-gray-500 text-white px-4 py-2 rounded cursor-pointer transition hover:underline">Limpar Carrinho</button>
                </form>

                <a href="{{ route('checkout') }}" class="bg-green-600 text-white px-4 py-2 rounded">Finalizar Compra</a>
            </div>
        </div>
    @endif
</div>
@endsection
