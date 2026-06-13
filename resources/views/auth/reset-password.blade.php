@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto my-12 bg-white p-8 rounded-lg shadow">
    <h2 class="text-2xl font-bold mb-2 text-center text-gray-800">Definir Nova Palavra-passe</h2>
    <p class="text-sm text-gray-600 text-center mb-6">
        Por favor, introduza o seu e-mail e a nova palavra-passe nos campos abaixo.
    </p>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-200 text-red-700 p-3 rounded mb-4 text-sm">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form action="{{ route('password.update_reset') }}" method="POST" class="space-y-4">
        @csrf
        
        <!-- Hidden Token -->
        <input type="hidden" name="token" value="{{ $token }}">

        <div>
            <label class="block text-sm font-medium text-gray-700">E-mail</label>
            <input type="email" name="email" value="{{ old('email', $email) }}" required readonly
                   class="mt-1 w-full border border-gray-300 bg-gray-50 text-gray-500 rounded px-3 py-2 focus:outline-none">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Nova Palavra-passe</label>
            <input type="password" name="password" required 
                   class="mt-1 w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-250 focus:outline-none">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Confirmar Nova Palavra-passe</label>
            <input type="password" name="password_confirmation" required 
                   class="mt-1 w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-250 focus:outline-none">
        </div>

        <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded font-semibold cursor-pointer transition hover:underline hover:bg-blue-700">
            Redefinir Palavra-passe
        </button>
    </form>
</div>
@endsection
