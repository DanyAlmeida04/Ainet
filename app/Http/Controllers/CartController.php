<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\TshirtImage;
use App\Models\Price;

class CartController extends Controller
{
    // Mostrar o conteúdo do carrinho
    public function index()
    {
        $cart = Session::get('cart', []);
        $priceConf = Price::current();
        return view('cart.index', compact('cart', 'priceConf'));
    }

    // Adicionar item ao carrinho
    public function add(Request $request)
    {
        $data = $request->validate([
            'tshirt_image_id' => 'required|exists:tshirt_images,id',
            'color_code' => 'required|string',
            'size' => 'required|in:XS,S,M,L,XL',
            'qty' => 'required|integer|min:1',
        ]);

        $itemKey = $data['tshirt_image_id'] . '|' . $data['color_code'] . '|' . $data['size'];

        $cart = Session::get('cart', []);

        if (isset($cart[$itemKey])) {
            $cart[$itemKey]['qty'] += $data['qty'];
        } else {
            $image = TshirtImage::find($data['tshirt_image_id']);
            $cart[$itemKey] = [
                'tshirt_image_id' => $data['tshirt_image_id'],
                'name' => $image->name,
                'image_url' => $image->image_url,
                'color_code' => $data['color_code'],
                'size' => $data['size'],
                'qty' => $data['qty'],
            ];
        }

        Session::put('cart', $cart);

        return redirect()->back()->with('success', 'Camisola adicionada com sucesso ao carrinho.');
    }

    // Atualizar quantidade/atributos de um item do carrinho
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
