<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;

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

        // Disparar evento de registo para enviar e-mail de verificação
        event(new \Illuminate\Auth\Events\Registered($user));

        // Fazer login automático ao utilizador acabado de criar
        Auth::login($user);

        return redirect()->route('catalog.index')->with('success', 'Conta criada com sucesso! Verifique o seu e-mail para confirmar a sua conta.');
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
        if (Auth::user()->user_type === 'F' || Auth::user()->user_type === 'E') {
            abort(403, 'Os funcionários não possuem permissões para alterar os dados da sua conta.');
        }
        $user = Auth::user();

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'nif' => 'nullable|digits:9',
            'address' => 'nullable|string|max:255',
            'photo_base64' => 'nullable|string',
        ]);

        // Update user name
        $user->name = $data['name'];

        // Handle profile photo upload and crop
        if ($request->filled('photo_base64')) {
            $base64 = $request->input('photo_base64');
            if (preg_match('/^data:image\/(\w+);base64,/', $base64, $matches)) {
                $type = strtolower($matches[1]);
                $base64 = substr($base64, strpos($base64, ',') + 1);
                $base64 = base64_decode($base64);

                if ($base64 !== false) {
                    $extension = in_array($type, ['jpeg', 'jpg', 'png', 'gif', 'webp']) ? $type : 'jpg';
                    $filename = $user->id . '_' . time() . '.' . $extension;

                    // Store photo in storage/app/public/photos
                    \Illuminate\Support\Facades\Storage::disk('public')->put('photos/' . $filename, $base64);

                    // Delete old photo if it exists and is not anonymous.png
                    if ($user->photo_url && $user->photo_url !== 'anonymous.png') {
                        \Illuminate\Support\Facades\Storage::disk('public')->delete('photos/' . $user->photo_url);
                    }

                    $user->photo_url = $filename;
                }
            }
        }

        $user->save();

        // Update customer details if customer exists
        if ($user->customer) {
            $user->customer->update([
                'nif' => $data['nif'] ?? null,
                'address' => $data['address'] ?? null,
            ]);
        }

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

    // New method to display user quick actions (for navbar)
    public function quickProfile()
    {
        $user = Auth::user()->load('customer');
        return view('auth.quick_profile', compact('user'));
    }

    // Exibir formulário de pedido de redefinição
    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    // Processar pedido de link de redefinição
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.exists' => 'Não encontramos nenhum utilizador registado com este endereço de e-mail.',
        ]);

        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('success', 'Enviámos por e-mail o link para redefinir a sua palavra-passe.');
        }

        return back()->withErrors(['email' => 'Ocorreu um erro ao tentar enviar o e-mail de recuperação.']);
    }

    // Exibir formulário para redefinir a senha usando o token
    public function showResetPassword(Request $request, $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    // Processar a redefinição de senha
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('success', 'A sua palavra-passe foi redefinida com sucesso.');
        }

        return back()->withErrors(['email' => 'O link de recuperação é inválido ou expirou.']);
    }
}
