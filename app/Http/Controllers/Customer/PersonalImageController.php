<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\TshirtImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class PersonalImageController extends Controller
{
    /**
     * Ensure user is authenticated, is a customer, and is not blocked.
     */
    protected function ensureCustomer()
    {
        $user = Auth::user();
        if (!$user || $user->user_type !== 'C' || ($user->blocked ?? false)) {
            abort(403, 'Acesso restrito a clientes ativos.');
        }
    }

    /**
     * Display a list of the customer's private images.
     */
    public function index()
    {
        $this->ensureCustomer();

        $images = TshirtImage::where('customer_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('customer.images.index', compact('images'));
    }

    /**
     * Store a new private custom image upload.
     */
    public function store(Request $request)
    {
        $this->ensureCustomer();

        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'image' => 'required|image|max:2048|mimes:png,jpeg,jpg,webp', // 2MB limit
        ]);

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            try {
                // Store in private/tshirt_images_private
                $path = $request->file('image')->store('private/tshirt_images_private');
                $filename = basename($path);

                TshirtImage::create([
                    'customer_id' => Auth::id(),
                    'category_id' => null, // Private images are not categorized
                    'name' => $request->name,
                    'description' => $request->description,
                    'image_url' => $filename,
                ]);

                return redirect()->route('profile.images.index')->with('success', 'Imagem pessoal adicionada com sucesso.');
            } catch (\Throwable $e) {
                Log::error('PersonalImageController@store upload error: ' . $e->getMessage());
                return back()->withInput()->withErrors(['image' => 'Ocorreu um erro ao gravar a imagem no servidor.']);
            }
        }

        return back()->withInput()->withErrors(['image' => 'Ficheiro de imagem inválido.']);
    }

    /**
     * Remove the specified private image.
     */
    public function destroy(TshirtImage $tshirtImage)
    {
        $this->ensureCustomer();

        // Check ownership
        if ($tshirtImage->customer_id !== Auth::id()) {
            abort(403, 'Não tem permissão para eliminar esta imagem.');
        }

        try {
            // Soft delete: keep the database record and the filesystem image file
            // so that older orders containing this custom print will continue to render previews correctly.
            $tshirtImage->delete();

            return redirect()->route('profile.images.index')->with('success', 'Imagem pessoal removida.');
        } catch (\Throwable $e) {
            Log::error('PersonalImageController@destroy error: ' . $e->getMessage());
            return back()->withErrors('Não foi possível remover a imagem.');
        }
    }
}
