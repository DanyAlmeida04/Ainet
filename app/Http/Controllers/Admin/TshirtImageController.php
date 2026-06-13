<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TshirtImage;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class TshirtImageController extends Controller
{
    /**
     * Ensure the authenticated user is an administrator.
     */
    protected function ensureAdmin()
    {
        $user = Auth::user();
        if (!$user || $user->user_type !== 'A' || ($user->blocked ?? false)) {
            abort(403, 'Acesso restrito a administradores ativos.');
        }
    }

    /**
     * List all public catalog designs (customer_id is null).
     */
    public function index(Request $request)
    {
        $this->ensureAdmin();

        $query = TshirtImage::whereNull('customer_id')->with('category');

        // Search by name or description
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        $designs = $query->orderBy('name')->paginate(12)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('admin.designs.index', compact('designs', 'categories'));
    }

    /**
     * Show form to create a new design.
     */
    public function create()
    {
        $this->ensureAdmin();
        $categories = Category::orderBy('name')->get();
        return view('admin.designs.create', compact('categories'));
    }

    /**
     * Store a new catalog design.
     */
    public function store(Request $request)
    {
        $this->ensureAdmin();

        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:1000',
            'category_id' => 'nullable|exists:categories,id',
            'image' => 'required|image|max:2048|mimes:png,jpeg,jpg,webp',
            'notify_customers' => 'nullable|boolean',
        ]);

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $file = $request->file('image');
            // Store file in public disk (tshirt_images directory)
            $path = $file->store('tshirt_images', 'public');
            $filename = basename($path);

            $tshirtImage = TshirtImage::create([
                'customer_id' => null, // Catalog design
                'category_id' => $request->category_id,
                'name' => $request->name,
                'description' => $request->description,
                'image_url' => $filename,
            ]);

            if ($request->boolean('notify_customers')) {
                $customers = \App\Models\User::where('user_type', 'C')->where('blocked', 0)->get();
                foreach ($customers as $customer) {
                    try {
                        \Illuminate\Support\Facades\Mail::to($customer->email)->send(new \App\Mail\NewDesignNotificationMailable($tshirtImage, $customer));
                    } catch (\Throwable $e) {
                        \Illuminate\Support\Facades\Log::error('Erro ao enviar email de novo design para ' . $customer->email . ': ' . $e->getMessage());
                    }
                }
            }

            return redirect()->route('admin.designs.index')->with('success', 'Design do catálogo criado com sucesso e notificações enviadas (se aplicável).');
        }

        return back()->withInput()->withErrors(['image' => 'Ficheiro de imagem inválido.']);
    }

    /**
     * Show form to edit an existing design.
     */
    public function edit(TshirtImage $tshirtImage)
    {
        $this->ensureAdmin();
        
        // Ensure this is a catalog design
        if ($tshirtImage->customer_id !== null) {
            abort(403, 'Não é possível editar imagens privadas dos clientes.');
        }

        $categories = Category::orderBy('name')->get();
        return view('admin.designs.edit', compact('tshirtImage', 'categories'));
    }

    /**
     * Update an existing catalog design.
     */
    public function update(Request $request, TshirtImage $tshirtImage)
    {
        $this->ensureAdmin();

        if ($tshirtImage->customer_id !== null) {
            abort(403, 'Não é possível alterar imagens privadas dos clientes.');
        }

        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:1000',
            'category_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image|max:2048|mimes:png,jpeg,jpg,webp',
        ]);

        $tshirtImage->name = $request->name;
        $tshirtImage->description = $request->description;
        $tshirtImage->category_id = $request->category_id;

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $file = $request->file('image');
            $path = $file->store('tshirt_images', 'public');
            $filename = basename($path);

            // Delete old file if exists
            if ($tshirtImage->image_url) {
                Storage::disk('public')->delete('tshirt_images/' . $tshirtImage->image_url);
            }

            $tshirtImage->image_url = $filename;
        }

        $tshirtImage->save();

        return redirect()->route('admin.designs.index')->with('success', 'Design do catálogo atualizado com sucesso.');
    }

    /**
     * Delete an existing catalog design.
     */
    public function destroy(TshirtImage $tshirtImage)
    {
        $this->ensureAdmin();

        if ($tshirtImage->customer_id !== null) {
            abort(403, 'Não é possível eliminar imagens privadas dos clientes.');
        }

        // Soft delete
        $tshirtImage->delete();

        return redirect()->route('admin.designs.index')->with('success', 'Design do catálogo removido com sucesso (soft-delete).');
    }
}
