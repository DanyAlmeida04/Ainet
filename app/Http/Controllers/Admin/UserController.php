<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    public function index()
    {
        // Only admins via middleware, but double-check via gate
        Gate::authorize('manage-users');

        $users = User::with('customer')->orderBy('user_type')->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    public function edit(User $user)
    {
        Gate::authorize('manage-users');
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        Gate::authorize('manage-users');

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
        Gate::authorize('manage-users');

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
        Gate::authorize('manage-users');
        $user->blocked = ! $user->blocked;
        $user->save();

        return back()->with('success', $user->blocked ? 'Utilizador bloqueado.' : 'Utilizador desbloqueado.');
    }
}
