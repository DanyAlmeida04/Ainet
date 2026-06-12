<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\TshirtImage;
use App\Models\Price;
use App\Models\UserTshirtImage;

class CartController extends Controller
{
    // Show cart contents
    public function index()
    {
        $cart = Session::get('cart', []);
        $priceConf = Price::current();
        return view('cart.index', compact('cart', 'priceConf'));
    }

    // Add item to cart
    public function add(Request $request)
    {
        $data = $request->validate([
            'tshirt_image_id' => 'required|integer',
            'color_code' => 'required|string',
            'size' => 'required|in:XS,S,M,L,XL',
            'qty' => 'required|integer|min:1',
            'is_custom' => 'sometimes|boolean',
        ]);

        $is_custom = $request->has('is_custom') && $data['is_custom'];
        $image_id = $data['tshirt_image_id'];

        if ($is_custom) {
            $image = UserTshirtImage::findOrFail($image_id);
            $this->authorize('view', $image);
            $price = Price::current()->unit_price_own;
        } else {
            $image = TshirtImage::findOrFail($image_id);
            $price = Price::current()->unit_price_catalog;
        }

        $itemKey = $image_id . '|' . $data['color_code'] . '|' . $data['size'] . '|' . (int)$is_custom;

        $cart = Session::get('cart', []);

        if (isset($cart[$itemKey])) {
            $cart[$itemKey]['qty'] += $data['qty'];
        } else {
            $cart[$itemKey] = [
                'tshirt_image_id' => $image_id,
                'name' => $image->name,
                'image_url' => $image->image_url,
                'color_code' => $data['color_code'],
                'size' => $data['size'],
                'qty' => $data['qty'],
                'is_custom' => $is_custom,
                'unit_price' => $price,
            ];
        }

        Session::put('cart', $cart);

        return redirect()->back()->with('success', 'Camisola adicionada com sucesso ao carrinho.');
    }

    // Update cart item quantity/attributes
    public function update(Request $request)
    {
        $data = $request->validate([
            'key' => 'required|string',
            'qty' => 'required|integer|min:0',
        ]);

        $cart = Session::get('cart', []);
        if (!isset($cart[$data['key']])) {
            return redirect()->back()->withErrors('Item não encontrado no carrinho.');
        }

        if ($data['qty'] == 0) {
            unset($cart[$data['key']]);
        } else {
            $cart[$data['key']]['qty'] = $data['qty'];
        }

        Session::put('cart', $cart);
        return redirect()->back()->with('success', 'Carrinho atualizado.');
    }

    public function remove(Request $request)
    {
        $data = $request->validate(['key' => 'required|string']);
        $cart = Session::get('cart', []);
        unset($cart[$data['key']]);
        Session::put('cart', $cart);
        return redirect()->back()->with('success', 'Item removido do carrinho.');
    }

    public function clear()
    {
        Session::forget('cart');
        return redirect()->back()->with('success', 'Carrinho limpo.');
    }
}
