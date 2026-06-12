@extends('layouts.admin')

@section('admin-content')
<div class="bg-white dark:bg-slate-900 rounded-2xl shadow border border-slate-200/60 dark:border-slate-800 p-6">
    <h2 class="text-xl font-bold text-slate-800 dark:text-white mb-6">Configuração de Preços</h2>
    <form method="post" action="{{ route('admin.prices.update') }}">
        @csrf
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block">Preço unitário (Catálogo)</label>
                <input type="text" name="unit_price_catalog" value="{{ old('unit_price_catalog', $price->unit_price_catalog ?? '10.00') }}" class="w-full p-2 border rounded">
            </div>
            <div>
                <label class="block">Preço unitário (Próprio)</label>
                <input type="text" name="unit_price_own" value="{{ old('unit_price_own', $price->unit_price_own ?? '8.00') }}" class="w-full p-2 border rounded">
            </div>
            <div>
                <label class="block">Desconto Catálogo</label>
                <input type="text" name="unit_price_catalog_discount" value="{{ old('unit_price_catalog_discount', $price->unit_price_catalog_discount ?? '') }}" class="w-full p-2 border rounded">
            </div>
            <div>
                <label class="block">Desconto Próprio</label>
                <input type="text" name="unit_price_own_discount" value="{{ old('unit_price_own_discount', $price->unit_price_own_discount ?? '') }}" class="w-full p-2 border rounded">
            </div>
            <div>
                <label class="block">Quantidade para desconto</label>
                <input type="number" name="qty_discount" value="{{ old('qty_discount', $price->qty_discount ?? '') }}" class="w-full p-2 border rounded">
            </div>
        </div>

        <div class="mt-4">
            <button class="px-4 py-2 bg-blue-700 text-white rounded">Salvar</button>
        </div>
    </form>
</div>
@endsection
