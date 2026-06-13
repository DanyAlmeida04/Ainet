@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto my-12 bg-white p-8 rounded-lg shadow">
    <h2 class="text-2xl font-bold mb-2 text-center text-gray-800">Recuperar Palavra-passe</h2>
    <p class="text-sm text-gray-600 text-center mb-6">
        Introduza o seu e-mail de registo para lhe enviarmos um link de redefinição de palavra-passe.
    </p>

    @if (session('success'))
        <div class="bg-green-100 border border-green-200 text-green-700 p-3 rounded mb-4 text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-100 border border-red-200 text-red-700 p-3 rounded mb-4 text-sm">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form action="{{ route('password.email') }}" method="POST" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700">E-mail</label>
            <input type="email" name="email" value="{{ old('email') }}" required 
                   class="mt-1 w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-250 focus:outline-none">
        </div>

        <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded font-semibold cursor-pointer transition hover:underline hover:bg-blue-700">
            Enviar Link de Recuperação
        </button>
    </form>
    
    <p class="text-sm text-center text-gray-600 mt-6">
        Voltar para o <a href="{{ route('login') }}" class="text-blue-600 hover:underline">iniciar sessão</a>.
    </p>
</div>
@endsection
