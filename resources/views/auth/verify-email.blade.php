@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto my-12 bg-white p-8 rounded-lg shadow">
    <h2 class="text-2xl font-bold mb-2 text-center text-gray-800">Confirme o seu E-mail</h2>
    <p class="text-sm text-gray-600 text-center mb-6">
        Antes de prosseguir com a sua compra, precisamos que verifique o seu endereço de e-mail clicando no link que lhe acabámos de enviar. Se não recebeu o e-mail, pode solicitar outro abaixo.
    </p>

    @if (session('success'))
        <div class="bg-green-100 border border-green-200 text-green-700 p-3 rounded mb-4 text-sm">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('verification.send') }}" method="POST" class="space-y-4">
        @csrf
        <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded font-semibold cursor-pointer transition hover:underline hover:bg-blue-700">
            Reenviar E-mail de Verificação
        </button>
    </form>
    
    <div class="border-t border-gray-200 mt-6 pt-4">
        <form action="{{ route('logout') }}" method="POST" class="text-center">
            @csrf
            <button type="submit" class="text-sm text-red-600 hover:underline cursor-pointer">
                Sair da Conta / Terminar Sessão
            </button>
        </form>
    </div>
</div>
@endsection
