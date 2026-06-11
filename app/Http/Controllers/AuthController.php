<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // 1. Mostrar o formulário de Login
    public function showLogin()
    {
        return view('auth.login');
    }

    // 2. Processar a tentativa de Login
    public function login(Request $request)
    {
        // Validação dos dados introduzidos
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Tentar autenticar o utilizador
        if (Auth::attempt($credentials, $request->remember)) {
            // Verificar se o utilizador está bloqueado (Requisito G1 do enunciado)
            if (Auth::user()->blocked) {
                Auth::logout();
                return back()->withErrors(['email' => 'A sua conta encontra-se bloqueada. Contacte o administrador.']);
            }

            // Regenerar a sessão por motivos de segurança
            $request->session()->regenerate();

            // Redireciona para o catálogo (ou para onde pretendia ir)
            return redirect()->intended(route('catalog.index'));
        }

        // Se falhar, volta atrás com erro
        return back()->withErrors([
            'email' => 'As credenciais introduzidas não correspondem aos nossos registos.',
        ])->withInput($request->only('email'));
    }

    // 3. Mostrar o formulário de Registo (Apenas para Clientes)
    public function showRegister()
    {
        return view('auth.register');
    }

    // 4. Processar o Registo do Cliente
    public function register(Request $request)
    {
        // Validação rigorosa dos dados (Unindo User e Customer)
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'nif' => 'nullable|digits:9', // Opcional no registo, mas se meter tem de ter 9 dígitos
            'address' => 'nullable|string|max:255',
            'gender' => 'nullable|in:M,F',
        ]);

        // Determine gender default if not provided
        $gender = $request->input('gender', 'M');

        // Criar o registo na tabela 'users'
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_type' => 'C', // 'C' de Cliente por defeito no registo público
            'blocked' => false,
            'gender' => $gender,
            'photo_url' => 'anonymous.png',
        ]);

        // Criar o registo correspondente na tabela 'customers' (Relação 1-para-1)
        // O ID do customer é exatamente o ID do user que acabou de ser criado
        Customer::create([
            'id' => $user->id,
            'nif' => $request->nif,
            'address' => $request->address,
        ]);

        // Fazer login automático ao utilizador acabado de criar
        Auth::login($user);

        return redirect()->route('catalog.index')->with('success', 'Conta criada com sucesso! Bem-vindo à FunShirt.');
    }

    // 5. Fazer Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('catalog.index');
    }

    // --- Profile & Password management ---
    public function showProfile()
    {
        $user = Auth::user()->load('customer');
        return view('auth.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'nif' => 'nullable|digits:9',
            'address' => 'nullable|string|max:255',
        ]);

        // Update user and customer
        $user->update(['name' => $data['name']]);
        $user->customer()->update([
            'nif' => $data['nif'] ?? null,
            'address' => $data['address'] ?? null,
        ]);

        return back()->with('success', 'Perfil atualizado com sucesso.');
    }

    public function showChangePassword()
    {
        return view('auth.password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (! Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'A password atual não corresponde.']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('success', 'Password alterada com sucesso.');
    }
}
