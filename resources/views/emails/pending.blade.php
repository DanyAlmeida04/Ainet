@extends('layouts.email')

@section('content')
    <div class="badge">Pendente</div>
    <h2>A sua encomenda está em processamento!</h2>
    <p>Olá <strong>{{ $order->customer->user->name ?? 'Cliente' }}</strong>,</p>
    <p>Confirmamos que a sua encomenda <strong>#{{ $order->id }}</strong> foi registada com sucesso e encontra-se agora na fila de estampagem e expedição.</p>
    
    <div class="card">
        <h3>Detalhes da Encomenda</h3>
        <p style="margin-bottom: 8px; font-size: 13px; color: #4b5563;"><strong>Número:</strong> #{{ $order->id }}</p>
        <p style="margin-bottom: 8px; font-size: 13px; color: #4b5563;"><strong>Morada de Envio:</strong> {{ $order->address }}</p>
        <p style="margin-bottom: 8px; font-size: 13px; color: #4b5563;"><strong>Total:</strong> {{ number_format($order->total_price, 2) }} €</p>
    </div>
    
    <p>Assim que a sua encomenda for expedida, receberá um novo e-mail com o respetivo recibo em formato PDF em anexo.</p>
    <p>Pode consultar o estado e detalhes da sua encomenda a qualquer momento na sua conta.</p>
    
    <div style="text-align: center; margin: 24px 0;">
        <a href="{{ route('orders.show', $order) }}" class="btn">Ver Encomenda na Loja</a>
    </div>

    <p style="border-top: 1px solid #e5e7eb; padding-top: 16px; margin-top: 24px; font-size: 13px; color: #6b7280; line-height: 1.5;">
        Cumprimentos,<br>
        <strong>Equipa FunShirt</strong>
    </p>
@endsection

