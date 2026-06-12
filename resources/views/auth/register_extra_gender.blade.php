@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto my-12 bg-white p-8 rounded-lg shadow">
    <h2 class="text-2xl font-bold mb-6 text-center text-gray-800">Criar Conta de Cliente</h2>

    @if ($errors->any())
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-sm">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form action="{{ route('register') }}" method="POST" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700">Nome Completo *</label>
            <input type="text" name="name" value="{{ old('name') }}" required
                   class="mt-1 w-full border border-gray-300 rounded px-3 py-2">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">E-mail *</label>
            <input type="email" name="email" value="{{ old('email') }}" required
                   class="mt-1 w-full border border-gray-300 rounded px-3 py-2">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Género</label>
            <select name="gender" class="mt-1 w-full border border-gray-300 rounded px-3 py-2">
                <option value="M" {{ old('gender') == 'M' ? 'selected' : '' }}>Masculino</option>
                <option value="F" {{ old('gender') == 'F' ? 'selected' : '' }}>Feminino</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">NIF (Opcional)</label>
            <input type="text" name="nif" value="{{ old('nif') }}" placeholder="9 dígitos"
                   class="mt-1 w-full border border-gray-300 rounded px-3 py-2">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Morada (Opcional)</label>
            <input type="text" name="address" value="{{ old('address') }}"
                   class="mt-1 w-full border border-gray-300 rounded px-3 py-2">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Palavra-passe *</label>
            <input type="password" name="password" required
                   class="mt-1 w-full border border-gray-300 rounded px-3 py-2">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Confirmar Palavra-passe *</label>
            <input type="password" name="password_confirmation" required
                   class="mt-1 w-full border border-gray-300 rounded px-3 py-2">
        </div>

        <button type="submit" class="w-full bg-green-600 text-white py-2 rounded font-semibold cursor-pointer transition hover:underline hover:bg-green-700">
            Registar Conta
        </button>
    </form>
</div>
@endsection
