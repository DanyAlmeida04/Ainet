@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-xl">
    <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200/60 dark:border-slate-800 shadow-sm text-center">
        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 dark:bg-blue-950/30 text-blue-600 dark:text-blue-400 mb-4">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m0-12.728l.707.707m12.728 12.728l.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z" />
            </svg>
        </div>

        <h2 class="text-xl font-bold mb-1 text-slate-800 dark:text-white">Novidade no Catálogo!</h2>
        <p class="text-xs text-slate-400 mb-4 uppercase tracking-wider">Temos novas estampas para si</p>

        <p class="text-sm text-slate-600 dark:text-slate-400 mb-6 text-left">
            Olá <strong>{{ $customer->name }}</strong>,
        </p>

        <p class="text-sm text-slate-600 dark:text-slate-400 mb-6 text-left leading-relaxed">
            Acabámos de adicionar uma nova estampa incrível ao nosso catálogo de t-shirts. Personalize a sua t-shirt hoje mesmo com este novo design:
        </p>

        {{-- Product spotlight card --}}
        <div class="bg-slate-50 dark:bg-slate-800/40 p-5 rounded-xl border border-slate-100 dark:border-slate-850 mb-6 flex flex-col items-center">
            @if($design->image_url)
                <div class="w-32 h-32 bg-white dark:bg-slate-900 rounded-xl border border-slate-200/60 dark:border-slate-700/60 flex items-center justify-center p-2 overflow-hidden shadow-sm mb-4">
                    <img src="{{ asset('storage/tshirt_images/' . $design->image_url) }}" alt="{{ $design->name }}" class="max-w-full max-h-full object-contain">
                </div>
            @endif

            <h3 class="text-base font-bold text-slate-800 dark:text-white mb-1">{{ $design->name }}</h3>
            @if($design->category)
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-blue-50 text-blue-700 dark:bg-blue-950/20 dark:text-blue-400 border border-blue-100 dark:border-blue-900/40 mb-3">
                    Categoria: {{ $design->category->name }}
                </span>
            @endif

            @if($design->description)
                <p class="text-xs text-slate-500 dark:text-slate-400 text-center italic leading-relaxed">
                    "{{ $design->description }}"
                </p>
            @endif
        </div>

        <div class="flex flex-col gap-2">
            <a href="{{ route('catalog.show', $design) }}" 
               class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 px-4 rounded-xl text-sm transition text-center shadow-sm hover:no-underline">
                Personalizar Esta T-Shirt
            </a>
            <a href="{{ route('catalog.index') }}" 
               class="w-full bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold py-2.5 px-4 rounded-xl text-sm transition text-center hover:no-underline">
                Ver Todo o Catálogo
            </a>
        </div>

        <p class="mt-8 text-xs text-slate-400 dark:text-slate-500 border-t border-slate-100 dark:border-slate-800 pt-4 text-left">
            Recebeu este e-mail porque tem uma conta de cliente ativa no site FunShirt.<br>
            Cumprimentos,<br><strong>Equipa FunShirt</strong>
        </p>
    </div>
</div>
@endsection
