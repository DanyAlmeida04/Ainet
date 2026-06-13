@extends('layouts.email')

@section('content')
    <div class="badge">Recibo</div>
    <h2>Obrigado pela sua encomenda!</h2>
    <p>Olá <strong>{{ $order->customer->user->name ?? 'Cliente' }}</strong>,</p>
    <p>Confirmamos a receção do seu pagamento para a encomenda <strong>#{{ $order->id }}</strong>. A sua fatura-recibo foi gerada com sucesso.</p>
    
    <div class="card">
        <h3>Detalhes da Encomenda</h3>
        <p style="margin-bottom: 8px; font-size: 13px; color: #4b5563;"><strong>Número:</strong> #{{ $order->id }}</p>
        <p style="margin-bottom: 8px; font-size: 13px; color: #4b5563;"><strong>Morada de Envio:</strong> {{ $order->address }}</p>
        <p style="margin-bottom: 8px; font-size: 13px; color: #4b5563;"><strong>Total Pago:</strong> {{ number_format($order->total_price, 2) }} €</p>
        @if($order->nif)
            <p style="margin-bottom: 8px; font-size: 13px; color: #4b5563;"><strong>NIF:</strong> {{ $order->nif }}</p>
        @endif
    </div>
    
    <p>Anexamos a este e-mail o respetivo recibo em formato PDF para seu arquivo.</p>
    <p>Pode também consultar o histórico e os detalhes de todas as suas encomendas diretamente na sua conta de cliente.</p>
    
    <div style="text-align: center; margin: 24px 0;">
        <a href="{{ route('orders.show', $order) }}" class="btn">Ver Encomenda na Loja</a>
    </div>

    <p style="border-top: 1px solid #e5e7eb; padding-top: 16px; margin-top: 24px; font-size: 13px; color: #6b7280; line-height: 1.5;">
        Cumprimentos,<br>
        <strong>Equipa FunShirt</strong>
    </p>
@endsection

