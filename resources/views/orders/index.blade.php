@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">As Minhas Encomendas</h1>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4">{{ session('success') }}</div>
    @endif

    @if($orders->count() == 0)
        <div class="bg-yellow-100 p-4 rounded">Ainda não tem encomendas.</div>
    @else
        <div class="space-y-4">
            @foreach($orders as $order)
                <div class="bg-white p-4 rounded shadow flex items-center justify-between">
                    <div>
                        <div class="font-semibold">Encomenda #{{ $order->id }} - {{ $order->status }}</div>
                        <div class="text-sm text-gray-600">Data: {{ $order->date->format('Y-m-d') }} - Total: €{{ number_format($order->total_price, 2) }}</div>
                    </div>
                    <div class="flex gap-2">
                        @if($order->receipt_url)
                            <a href="{{ route('orders.receipt', $order) }}" target="_blank" class="bg-blue-600 text-white px-3 py-1 rounded">Ver Recibo</a>
                            <form action="{{ route('orders.resendReceipt', $order) }}" method="POST" class="resend-form">
                                @csrf
                                <button type="button" data-order-id="{{ $order->id }}" class="bg-gray-500 text-white px-3 py-1 rounded resend-btn">Reenviar Recibo</button>
                            </form>
                        @else
                            <span class="text-sm text-gray-500">Recibo indisponível (a gerar)</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">{{ $orders->links() }}</div>
    @endif
</div>

<!-- Modal -->
<div id="confirmModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
    <div class="bg-white p-6 rounded shadow max-w-md w-full">
        <h3 class="text-lg font-semibold mb-4">Confirmar Reenvio</h3>
        <p>Tem a certeza que quer reenviar o recibo por e-mail? Esta ação está limitada.</p>
        <div class="mt-4 flex justify-end gap-2">
            <button id="cancelResend" class="px-4 py-2 rounded bg-gray-200">Cancelar</button>
            <button id="confirmResend" class="px-4 py-2 rounded bg-blue-600 text-white">Confirmar</button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('confirmModal');
        let currentForm = null;

        document.querySelectorAll('.resend-btn').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                currentForm = btn.closest('form');
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            });
        });

        document.getElementById('cancelResend').addEventListener('click', function() {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            currentForm = null;
        });

        document.getElementById('confirmResend').addEventListener('click', function() {
            if (currentForm) {
                currentForm.submit();
            }
        });
    });
</script>
@endsection
