@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto my-12 bg-white p-8 rounded-lg shadow">
    <h2 class="text-2xl font-bold mb-6 text-center text-gray-800">Iniciar Sessão</h2>

    @if ($errors->any())
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-sm">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form action="{{ route('login') }}" method="POST" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700">E-mail</label>
            <input type="email" name="email" value="{{ old('email') }}" required 
                   class="mt-1 w-full border border-gray-300 rounded px-3 py-2">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Palavra-passe</label>
            <input type="password" name="password" required 
                   class="mt-1 w-full border border-gray-300 rounded px-3 py-2">
        </div>

        <div class="flex items-center">
            <input type="checkbox" name="remember" id="remember" class="mr-2">
            <label Soluções para "remember" class="text-sm text-gray-600" for="remember">Lembrar-me neste computador</label>
        </div>

        <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded font-semibold hover:bg-blue-700">
            Entrar
        </button>
    </form>
    
    <p class="text-sm text-center text-gray-600 mt-4">
        Ainda não tem conta? <a href="{{ route('register') }}" class="text-blue-600 hover:underline">Registe-se aqui</a>.
    </p>
</div>
@endsection