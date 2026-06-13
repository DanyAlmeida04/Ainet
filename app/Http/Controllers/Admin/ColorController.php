<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Color;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ColorController extends Controller
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
        $colors = Color::orderBy('name')->paginate(20);
        return view('admin.colors.index', compact('colors'));
    }

    public function create()
    {
        $this->ensureAdmin();
        return view('admin.colors.create');
    }

    public function store(Request $request)
    {
        $this->ensureAdmin();

        $data = $request->validate([
            'code' => [
                'required',
                'string',
                'max:50',
                function ($attribute, $value, $fail) {
                    $existing = Color::withTrashed()->find($value);
                    if ($existing && !$existing->trashed()) {
                        $fail('O código de cor já está em uso.');
                    }
                }
            ],
            'name' => 'required|string|max:255',
            'image' => 'required|image|max:2048',
        ]);

        $code = $data['code'];

        // If the color exists in trashed, restore it, otherwise create a new one
        $color = Color::withTrashed()->find($code);
        if ($color) {
            $color->restore();
            $color->name = $data['name'];
            $color->save();
        } else {
            $color = Color::create([
                'code' => $code,
                'name' => $data['name'],
            ]);
        }

        // Upload and save the base shirt image
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            Storage::disk('public')->putFileAs('tshirt_base', $file, $code . '.jpg');
        }

        return redirect()->route('admin.colors.index')->with('success', 'Cor criada com sucesso.');
    }

    public function edit(Color $color)
    {
        $this->ensureAdmin();
        return view('admin.colors.edit', compact('color'));
    }

    public function update(Request $request, Color $color)
    {
        $this->ensureAdmin();

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|max:2048',
        ]);

        $color->name = $data['name'];
        $color->save();

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            Storage::disk('public')->putFileAs('tshirt_base', $file, $color->code . '.jpg');
        }

        return redirect()->route('admin.colors.index')->with('success', 'Cor atualizada com sucesso.');
    }

    public function destroy(Color $color)
    {
        $this->ensureAdmin();
        $color->delete();
        return redirect()->route('admin.colors.index')->with('success', 'Cor excluída (soft delete) com sucesso.');
    }
}
