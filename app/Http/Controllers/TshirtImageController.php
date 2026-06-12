<?php

namespace App\Http\Controllers;

use App\Models\TshirtImage;
use App\Models\Category;
use App\Models\Color;
use App\Models\Price;
use Illuminate\Http\Request;

class TshirtImageController extends Controller
{
    /**
     * Exibe o catálogo público de t-shirts.
     */
    public function index(Request $request)
    {
        // 1. Iniciamos a query filtrando apenas pelas imagens públicas do catálogo (customer_id é null)
        // Usamos o select() para trazer apenas as colunas necessárias e otimizar a performance
        $query = TshirtImage::select('id', 'name', 'description', 'image_url', 'category_id')
            ->whereNull('customer_id');

        // 2. Se o utilizador pesquisar por nome ou descrição (Requisito G2 do enunciado)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // 3. Se o utilizador filtrar por categoria
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // 4. Executa a query trazendo os resultados paginados (ex: 12 t-shirts por página)
        $tshirtImages = $query->paginate(12)->withQueryString();

        // 5. Vamos também buscar as categorias existentes para preencher a lista de filtros no ecrã
        $categories = Category::select('id', 'name')->orderBy('name')->get();

        // 6. Envia os dados para a vista Blade que vamos criar a seguir
        return view('catalog.index', compact('tshirtImages', 'categories'));
    }

    /**
     * Exibe a página de detalhes de uma t-shirt.
     */
    public function show(TshirtImage $tshirtImage)
    {
        // Impedir o acesso a imagens personalizadas privadas de outros clientes
        if ($tshirtImage->isPrivate()) {
            $user = auth()->user();
            $customer = $user ? $user->customer : null;
            $isOwner = $customer && $customer->id === $tshirtImage->customer_id;
            $isAdminOrStaff = $user && ($user->user_type === 'A' || $user->user_type === 'E');

            if (!$isOwner && !$isAdminOrStaff) {
                abort(403, 'Não tem permissão para aceder a esta imagem.');
            }
        }

        // Obter todas as cores ativas
        $colors = Color::orderBy('name')->get();

        // Obter a configuração de preços em vigor
        $priceConf = Price::current();

        return view('catalog.show', compact('tshirtImage', 'colors', 'priceConf'));
    }

    /**
     * Stream a private t-shirt image safely to authenticated users with permissions.
     */
    public function streamPrivateImage($filename)
    {
        $path = 'private/tshirt_images_private/' . $filename;

        if (! \Illuminate\Support\Facades\Storage::exists($path)) {
            abort(404, 'Imagem não encontrada.');
        }

        // Find the record in tshirt_images table
        $tshirtImage = TshirtImage::where('image_url', $filename)->first();

        if ($tshirtImage && $tshirtImage->isPrivate()) {
            $user = auth()->user();
            $isOwner = $user && $user->id === $tshirtImage->customer_id;
            $isAdminOrStaff = $user && ($user->user_type === 'A' || $user->user_type === 'E');

            if (!$isOwner && !$isAdminOrStaff) {
                abort(403, 'Não tem permissão para aceder a esta imagem.');
            }
        }

        $fullPath = \Illuminate\Support\Facades\Storage::path($path);

        return response()->file($fullPath, [
            'Content-Type' => \Illuminate\Support\Facades\Storage::mimeType($path) ?? 'image/png',
            'Cache-Control' => 'private, max-age=86400',
        ]);
    }
}