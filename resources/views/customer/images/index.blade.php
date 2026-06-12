@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8 border-b border-slate-200 dark:border-slate-800 pb-5">
        <h1 class="text-3xl font-bold text-slate-800 dark:text-white">Minhas Imagens Personalizadas</h1>
        <p class="text-slate-500 dark:text-slate-400 mt-1">Carregue e organize as suas imagens e designs de uso exclusivo para estampagem nas suas t-shirts.</p>
    </div>

    {{-- Session success/error notices --}}
    @if(session('success'))
        <div class="mb-6 flex items-center gap-3 p-4 bg-emerald-50 dark:bg-emerald-950/20 text-emerald-800 dark:text-emerald-300 rounded-xl border border-emerald-200/60 dark:border-emerald-900/60 shadow-sm animate-fade-in">
            <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="text-sm font-semibold">{{ session('success') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 p-4 bg-rose-50 dark:bg-rose-950/20 text-rose-800 dark:text-rose-300 rounded-xl border border-rose-200/60 dark:border-rose-900/60 shadow-sm">
            <ul class="list-disc list-inside text-xs space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Left: Upload Form --}}
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200/60 dark:border-slate-800 p-6 sticky top-6">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-4">Adicionar Novo Design</h3>
                
                <form action="{{ route('profile.images.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Nome do Design</label>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="Ex: Minha Foto, Logótipo Banda..." required
                               class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:text-slate-200">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Descrição (Opcional)</label>
                        <textarea name="description" placeholder="Uma breve descrição sobre a imagem..." rows="3"
                                  class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:text-slate-200"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Imagem de Estampagem</label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-slate-300 dark:border-slate-700 border-dashed rounded-xl hover:border-slate-400 dark:hover:border-slate-500 transition duration-150">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-10 w-10 text-slate-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-slate-600 dark:text-slate-400">
                                    <label for="image-upload" class="relative cursor-pointer bg-white dark:bg-slate-900 rounded-md font-semibold text-blue-600 dark:text-blue-400 hover:text-blue-500 focus-within:outline-none">
                                        <span>Carregar ficheiro</span>
                                        <input id="image-upload" name="image" type="file" required class="sr-only" accept="image/*">
                                    </label>
                                    <p class="pl-1">ou arrastar e soltar</p>
                                </div>
                                <p class="text-xs text-slate-500 dark:text-slate-400">PNG, JPG, WEBP até 2MB</p>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 px-4 rounded-xl text-sm transition duration-150 shadow-sm cursor-pointer text-center">
                        Gravar e Adicionar
                    </button>
                </form>
            </div>
        </div>

        {{-- Right: Images Grid --}}
        <div class="lg:col-span-2">
            @if($images->isEmpty())
                <div class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-2xl p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375 0 11-.75 0 .375 0 01.75 0z" />
                    </svg>
                    <h3 class="mt-4 text-sm font-semibold text-slate-900 dark:text-white">Sem imagens personalizadas</h3>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Comece por carregar a sua primeira imagem à esquerda para criar t-shirts únicas.</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                    @foreach($images as $img)
                        <div class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-2xl overflow-hidden hover:shadow-md transition-shadow duration-200 flex flex-col justify-between">
                            
                            {{-- Image Preview --}}
                            <div class="aspect-square bg-slate-50 dark:bg-slate-950 flex items-center justify-center p-6 relative group border-b border-slate-100 dark:border-slate-800/60">
                                <img src="{{ route('tshirt-images.private', ['filename' => $img->image_url]) }}" alt="{{ $img->name }}" class="max-h-full max-w-full object-contain drop-shadow-md">
                                <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition-opacity duration-150 flex items-center justify-center gap-2">
                                    <a href="{{ route('catalog.show', $img) }}" class="bg-white text-slate-950 text-xs font-semibold py-2 px-3.5 rounded-lg shadow hover:bg-slate-100 transition">
                                        Usar no Catálogo
                                    </a>
                                </div>
                            </div>

                            {{-- Details --}}
                            <div class="p-4 flex-1 flex flex-col justify-between">
                                <div>
                                    <h4 class="font-bold text-slate-800 dark:text-slate-250 truncate" title="{{ $img->name }}">{{ $img->name }}</h4>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 line-clamp-2 h-8 leading-normal">{{ $img->description ?: 'Sem descrição.' }}</p>
                                </div>

                                <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800/60 flex items-center justify-between">
                                    <span class="text-[10px] text-slate-400 font-semibold">{{ $img->created_at ? $img->created_at->format('d/m/Y') : 'N/A' }}</span>
                                    
                                    <form action="{{ route('profile.images.destroy', $img) }}" method="POST" onsubmit="return confirm('Tem a certeza que deseja remover esta imagem?');">
                                        @csrf
                                        <button type="submit" class="text-xs font-semibold text-rose-600 hover:text-rose-700 cursor-pointer">
                                            Eliminar
                                        </button>
                                    </form>
                                </div>
                            </div>

                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                @if(method_exists($images, 'links'))
                    <div class="mt-8">
                        {{ $images->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection
