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
        ]);

        // Criar o registo na tabela 'users'
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_type' => 'C', // 'C' de Cliente por defeito no registo público
            'blocked' => false,
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
}