<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Price;
use Illuminate\Support\Facades\Gate;

class PriceController extends Controller
{
    public function edit()
    {
        Gate::authorize('manage-users');
        $price = Price::find(1);
        return view('admin.prices.edit', compact('price'));
    }

    public function update(Request $request)
    {
        Gate::authorize('manage-users');
        $data = $request->validate([
            'unit_price_catalog' => 'required|numeric|min:0',
            'unit_price_own' => 'required|numeric|min:0',
            'unit_price_catalog_discount' => 'nullable|numeric|min:0',
            'unit_price_own_discount' => 'nullable|numeric|min:0',
            'qty_discount' => 'nullable|integer|min:0',
        ]);

        $price = Price::find(1);
        if (! $price) { $price = new Price(); $price->id = 1; }

        $price->fill($data);
        $price->save();

        return redirect()->route('admin.dashboard')->with('success', 'Preços atualizados.');
    }
}
