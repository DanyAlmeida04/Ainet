<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    protected function ensureAdmin()
    {
        $user = Auth::user();
        if (! $user || strtoupper((string)($user->user_type ?? '')) !== 'A' || ($user->blocked)) {
            abort(403, 'Ação restrita a administradores.');
        }
    }

    public function index()
    {
        $this->ensureAdmin();
        $categories = Category::orderBy('name')->paginate(20);
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        $this->ensureAdmin();
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $this->ensureAdmin();

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|max:2048',
        ]);

        $path = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $name = time() . '_' . $file->getClientOriginalName();
            $path = 'categories/' . $name;
            Storage::putFileAs('public/categories', $file, $name);
        }

        Category::create([
            'name' => $data['name'],
            'image_url' => $path ? basename($path) : null,
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Categoria criada.');
    }

    public function edit(Category $category)
    {
        $this->ensureAdmin();
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $this->ensureAdmin();

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $name = time() . '_' . $file->getClientOriginalName();
            Storage::putFileAs('public/categories', $file, $name);
            $category->image_url = basename($name);
        }

        $category->name = $data['name'];
        $category->save();

        return redirect()->route('admin.categories.index')->with('success', 'Categoria atualizada.');
    }

    public function destroy(Category $category)
    {
        $this->ensureAdmin();
        $category->delete();
        return back()->with('success', 'Categoria removida.');
    }
}
