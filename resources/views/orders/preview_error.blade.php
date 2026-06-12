@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white p-6 rounded shadow">
        <h2 class="text-xl font-bold mb-4">Preview do Recibo</h2>
        <div class="text-red-600 mb-4">{{ $message }}</div>
        @if(!empty($path))
            <div class="mb-4 text-sm text-gray-600">Caminho detectado: <code>{{ $path }}</code></div>
        @endif
        <p>Opções:</p>
        <ul class="list-disc pl-6">
            <li>
                <form method="post" action="{{ route('orders.resendReceipt', $order) }}">
                    @csrf
                    <button type="submit" class="bg-green-600 text-white px-3 py-1 rounded">Gerar e Enviar Recibo</button>
                </form>
            </li>
            <li><a href="{{ route('orders.index') }}" class="text-blue-600">Voltar ao histórico de encomendas</a></li>
        </ul>
    </div>
</div>
@endsection
