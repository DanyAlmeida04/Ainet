<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function __construct()
    {
        Gate::authorize('manage-categories');
    }

    public function index()
    {
        $categories = Category::orderBy('name')->paginate(20);
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
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
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
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
        $category->delete();
        return back()->with('success', 'Categoria removida.');
    }
}
