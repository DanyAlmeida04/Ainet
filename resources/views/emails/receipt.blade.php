@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white p-6 rounded shadow">
        <h2 class="text-xl font-bold mb-2">Obrigado pela sua encomenda</h2>
        <p>Olá {{ $order->customer->user->name ?? 'Cliente' }},</p>
        <p>Anexo encontra o recibo da sua encomenda #{{ $order->id }}. Pode também ver o recibo através da sua conta.</p>
        <p class="mt-4">Cumprimentos,<br>FunShirt</p>
    </div>
</div>
@endsection
