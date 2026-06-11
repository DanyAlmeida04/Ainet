@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto my-12 bg-white p-8 rounded-lg shadow">
    <h2 class="text-2xl font-bold mb-6 text-center text-gray-800">Alterar Palavra-passe</h2>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4 text-sm">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-sm">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form action="{{ route('password.update') }}" method="POST" class="space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-medium text-gray-700">Password Atual</label>
            <input type="password" name="current_password" required class="mt-1 w-full border border-gray-300 rounded px-3 py-2">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Nova Password</label>
            <input type="password" name="password" required class="mt-1 w-full border border-gray-300 rounded px-3 py-2">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Confirmar Nova Password</label>
            <input type="password" name="password_confirmation" required class="mt-1 w-full border border-gray-300 rounded px-3 py-2">
        </div>

        <button type="submit" class="w-full bg-green-600 text-white py-2 rounded font-semibold hover:bg-green-700">
            Alterar Password
        </button>
    </form>
</div>
@endsection
