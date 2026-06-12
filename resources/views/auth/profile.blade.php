@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto my-12 bg-white p-8 rounded-lg shadow">
    <h2 class="text-2xl font-bold mb-6 text-center text-gray-800">Perfil</h2>

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

    <div class="grid grid-cols-2 gap-6">
        <div>
            <form action="{{ route('profile.update') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nome Completo *</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                           class="mt-1 w-full border border-gray-300 rounded px-3 py-2">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">NIF</label>
                    <input type="text" name="nif" value="{{ old('nif', $user->customer->nif ?? '') }}"
                           class="mt-1 w-full border border-gray-300 rounded px-3 py-2">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Morada</label>
                    <input type="text" name="address" value="{{ old('address', $user->customer->address ?? '') }}"
                           class="mt-1 w-full border border-gray-300 rounded px-3 py-2">
                </div>

                <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded font-semibold hover:bg-blue-700">
                    Atualizar Perfil
                </button>
            </form>

            <p class="text-sm text-center text-gray-600 mt-4">
                <a href="{{ route('password.show') }}" class="text-blue-600 hover:underline">Alterar Password</a>
            </p>
        </div>

        <div>
            <div class="bg-gray-50 p-4 rounded">
                <h3 class="font-bold">Resumo</h3>
                @php
                    $ordersCount = $user->customer ? $user->customer->orders()->count() : 0;
                    $totalSpent = $user->customer ? $user->customer->orders()->where('status','closed')->sum('total_price') : 0;
                    $lastOrder = $user->customer ? $user->customer->orders()->orderBy('date','desc')->first() : null;
                @endphp

                <p>Encomendas: <strong>{{ $ordersCount }}</strong></p>
                <p>Total gasto (fechadas): <strong>€ {{ number_format($totalSpent,2) }}</strong></p>
                @if($lastOrder)
                    <p>Última encomenda: #{{ $lastOrder->id }} em {{ $lastOrder->date->format('Y-m-d') }}</p>
                @endif
            </div>

            <div class="mt-4 bg-white p-4 rounded shadow">
                <h3 class="font-bold mb-2">Últimas Encomendas</h3>
                @php
                    $recent = $user->customer ? $user->customer->orders()->orderBy('date','desc')->limit(5)->get() : collect();
                @endphp
                @foreach($recent as $o)
                    <div class="border-b py-2">
                        <div class="flex items-center">
                            <div>#{{ $o->id }} - {{ $o->date->format('Y-m-d') }}</div>
                            <div class="ml-auto">€ {{ number_format($o->total_price,2) }}</div>
                        </div>
                        <div class="text-sm text-gray-600">Status: {{ $o->status }}</div>
                    </div>
                @endforeach

                <p class="mt-3 text-right"><a href="{{ route('orders.index') }}" class="text-sm text-blue-600">Ver histórico completo</a></p>
            </div>
        </div>
    </div>
</div>
@endsection
