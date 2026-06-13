<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    protected function ensureAdmin()
    {
        $user = Auth::user();
        if (! $user || $user->user_type !== 'A' || ($user->blocked ?? false)) {
            abort(403, 'Ação restrita a administradores.');
        }
    }

    public function index(Request $request)
    {
        // Only admins via middleware, but double-check via explicit check
        $this->ensureAdmin();

        $q = User::with('customer');

        if ($request->filled('search')) {
            $search = $request->search;
            $q->where(function($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('user_type')) {
            $q->where('user_type', $request->user_type);
        }

        if ($request->filled('blocked')) {
            $q->where('blocked', $request->blocked);
        }

        $users = $q->orderBy('user_type')->paginate(20)->appends($request->query());
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $this->ensureAdmin();
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $this->ensureAdmin();

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'user_type' => 'required|in:C,E,A',
            'gender' => 'required|in:M,F',
            'blocked' => 'nullable|boolean',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'user_type' => $data['user_type'],
            'gender' => $data['gender'],
            'blocked' => $data['blocked'] ?? false,
            'photo_url' => 'anonymous.png',
        ]);

        if ($user->user_type === 'C') {
            \App\Models\Customer::create([
                'id' => $user->id,
                'nif' => null,
                'address' => null,
            ]);
        }

        return redirect()->route('admin.users.index')->with('success', 'Utilizador criado com sucesso.');
    }

    public function edit(User $user)
    {
        $this->ensureAdmin();
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $this->ensureAdmin();

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'user_type' => 'required|in:C,E,A',
            'blocked' => 'nullable|boolean',
        ]);

        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'user_type' => $data['user_type'],
            'blocked' => $data['blocked'] ?? false,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Utilizador atualizado.');
    }

    public function destroy(User $user)
    {
        $this->ensureAdmin();

        // Soft delete if user has orders or tshirt images; otherwise force delete
        $hasOrders = $user->id && $user->customer && $user->customer->orders()->exists();
        $hasImages = $user->id && $user->customer && $user->customer->tshirtImages()->exists();

        if ($hasOrders || $hasImages) {
            $user->delete(); // soft delete
        } else {
            $user->forceDelete();
        }

        return redirect()->route('admin.users.index')->with('success', 'Utilizador removido.');
    }

    // Block/unblock quick action
    public function toggleBlock(User $user)
    {
        $this->ensureAdmin();
        $user->blocked = ! ($user->blocked ?? false);
        $user->save();

        return back()->with('success', $user->blocked ? 'Utilizador bloqueado.' : 'Utilizador desbloqueado.');
    }
}
