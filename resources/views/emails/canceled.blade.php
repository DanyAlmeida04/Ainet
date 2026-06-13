@extends('layouts.email')

@section('content')
    <div class="badge" style="background-color: #fee2e2; color: #991b1b;">Cancelada</div>
    <h2 style="color: #b91c1c;">A sua encomenda foi anulada</h2>
    <p>Olá <strong>{{ $order->customer->user->name ?? 'Cliente' }}</strong>,</p>
    <p>Informamos que a sua encomenda <strong>#{{ $order->id }}</strong> foi anulada e o seu estado foi atualizado para cancelado.</p>
    
    @if($order->reason_for_cancellation)
        <div class="card" style="background-color: #fff5f5; border-color: #fee2e2;">
            <h3 style="color: #991b1b; margin-bottom: 8px;">Motivo da Anulação</h3>
            <p style="font-size: 13px; color: #7f1d1d; font-style: italic; margin-bottom: 0;">
                "{{ $order->reason_for_cancellation }}"
            </p>
        </div>
    @endif
    
    <p>Se tiver alguma dúvida ou pretender informações adicionais sobre este processo, por favor não hesite em contactar a nossa equipa de suporte.</p>
    
    <div style="text-align: center; margin: 24px 0;">
        <a href="{{ route('orders.show', $order) }}" class="btn-secondary">Ver Detalhes na Loja</a>
    </div>

    <p style="border-top: 1px solid #e5e7eb; padding-top: 16px; margin-top: 24px; font-size: 13px; color: #6b7280; line-height: 1.5;">
        Cumprimentos,<br>
        <strong>Equipa FunShirt</strong>
    </p>
@endsection

