<?php

namespace App\Http\Controllers;

use App\Models\TshirtImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserTshirtImageController extends Controller
{
    public function index()
    {
        $customer = Auth::user()->customer;
        if (!$customer) {
            return redirect()->route('home')->with('error', 'Apenas clientes podem ter imagens personalizadas.');
        }
        $tshirt_images = $customer->tshirt_images()->orderBy('created_at', 'desc')->paginate(10);
        return view('user.tshirt_images.index', compact('tshirt_images'));
    }

    public function create()
    {
        return view('user.tshirt_images.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $customer = Auth::user()->customer;

        $path = $request->file('image')->store('tshirt_images_private', 'private');

        $customer->tshirt_images()->create([
            'name' => $request->name,
            'description' => $request->description,
            'image_url' => $path,
        ]);

        return redirect()->route('user.tshirt_images.index')->with('success', 'Imagem enviada com sucesso.');
    }

    public function edit(TshirtImage $tshirt_image)
    {
        $this->authorize('update', $tshirt_image);
        return view('user.tshirt_images.edit', compact('tshirt_image'));
    }

    public function update(Request $request, TshirtImage $tshirt_image)
    {
        $this->authorize('update', $tshirt_image);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $tshirt_image->update($request->only('name', 'description'));

        return redirect()->route('user.tshirt_images.index')->with('success', 'Imagem atualizada com sucesso.');
    }

    public function destroy(TshirtImage $tshirt_image)
    {
        $this->authorize('delete', $tshirt_image);

        Storage::disk('private')->delete($tshirt_image->image_url);
        $tshirt_image->delete();

        return redirect()->route('user.tshirt_images.index')->with('success', 'Imagem eliminada com sucesso.');
    }
}
