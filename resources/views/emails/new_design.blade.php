@extends('layouts.email')

@section('content')
    <div class="badge">Novidade</div>
    <h2>Novidade no Nosso Catálogo!</h2>
    
    <p>Olá <strong>{{ $customer->name }}</strong>,</p>
    <p>Temos o prazer de anunciar que acabámos de adicionar um novo design exclusivo ao nosso catálogo de t-shirts. Personalize a sua t-shirt hoje mesmo com esta nova estampa:</p>
    
    <div class="card" style="text-align: center; padding: 24px;">
        @if($design->image_url)
            <div style="margin: 0 auto 16px auto; width: 160px; height: 160px; background-color: #ffffff; border: 1px solid #e5e7eb; border-radius: 12px; display: flex; align-items: center; justify-content: center; padding: 8px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                <img src="{{ asset('storage/tshirt_images/' . $design->image_url) }}" alt="{{ $design->name }}" style="max-width: 100%; max-height: 100%; object-fit: contain; display: block; margin: 0 auto;">
            </div>
        @endif
        
        <h3 style="margin-bottom: 4px; font-size: 18px; color: #111827;">{{ $design->name }}</h3>
        
        @if($design->category)
            <span style="display: inline-block; padding: 2px 8px; border-radius: 9999px; font-size: 11px; font-weight: bold; background-color: #eff6ff; color: #1d4ed8; border: 1px solid #dbeafe; margin-bottom: 12px;">
                Categoria: {{ $design->category->name }}
            </span>
        @endif
        
        @if($design->description)
            <p style="font-size: 13px; color: #6b7280; font-style: italic; margin-bottom: 0; max-width: 400px; margin-left: auto; margin-right: auto; line-height: 1.4;">
                "{{ $design->description }}"
            </p>
        @endif
    </div>
    
    <div style="text-align: center; margin: 24px 0;">
        <a href="{{ route('catalog.show', $design) }}" class="btn" style="display: inline-block; width: 220px; margin-bottom: 8px;">Personalizar Esta T-Shirt</a>
        <br>
        <a href="{{ route('catalog.index') }}" class="btn-secondary" style="display: inline-block; width: 220px;">Ver Todo o Catálogo</a>
    </div>

    <p style="border-top: 1px solid #e5e7eb; padding-top: 16px; margin-top: 24px; font-size: 12px; color: #9ca3af; line-height: 1.4;">
        Recebeu este e-mail porque está registado para receber novidades da FunShirt.<br>
        Se pretender deixar de receber estes e-mails, pode atualizar as suas preferências no seu perfil.<br>
        Cumprimentos,<br>
        <strong>Equipa FunShirt</strong>
    </p>
@endsection

