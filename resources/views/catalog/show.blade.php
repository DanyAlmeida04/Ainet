@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    {{-- Voltar ao Catálogo --}}
    <a href="{{ route('catalog.index') }}" class="inline-flex items-center text-sm font-semibold hover:underline mb-6 text-slate-600 dark:text-slate-400 gap-1 transition">
        &larr; Voltar ao Catálogo
    </a>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 bg-white dark:bg-slate-900 rounded-3xl shadow-md border border-slate-200/50 dark:border-slate-800/50 p-6 md:p-10">
        {{-- Left Column: T-Shirt Preview --}}
        <div class="flex flex-col items-center">
            @php
                // Prefer white color 'fafafa' as default if available, otherwise first color
                $defaultColor = $colors->firstWhere('code', 'fafafa') ?? $colors->first();
                $defaultColorCode = $defaultColor ? $defaultColor->code : 'fafafa';
                $defaultColorName = $defaultColor ? $defaultColor->name : 'Branco';
            @endphp
            <div class="relative w-full aspect-square max-w-md bg-slate-50 dark:bg-slate-950 rounded-2xl shadow-inner border border-slate-200/40 dark:border-slate-800/40 flex items-center justify-center p-6 overflow-hidden">
                {{-- Base T-shirt Image --}}
                <img id="base-tshirt" 
                     src="{{ asset('storage/tshirt_base/' . $defaultColorCode . '.jpg') }}" 
                     alt="Base T-shirt Color" 
                     class="w-full h-full object-contain pointer-events-none select-none transition-all duration-300">
                
                {{-- Catalog Design Overlay (absolute position on chest area) --}}
                <img src="{{ $tshirtImage->isPrivate() ? route('tshirt-images.private', ['filename' => $tshirtImage->image_url]) : asset('storage/tshirt_images/' . $tshirtImage->image_url) }}" 
                     alt="{{ $tshirtImage->name }}" 
                     class="absolute w-[36%] h-[36%] object-contain top-[28%] left-1/2 -translate-x-1/2 pointer-events-none select-none drop-shadow-sm opacity-90 transition-opacity">
            </div>
            
            <p class="text-xs text-slate-400 dark:text-slate-500 mt-4 text-center">
                * Imagem simulada de estampagem
            </p>
        </div>

        {{-- Right Column: Information & Configuration Form --}}
        <div class="flex flex-col justify-between">
            <div>
                {{-- Category tag --}}
                <span class="inline-block px-3 py-1 text-xs font-semibold bg-blue-50 text-blue-800 dark:bg-blue-950/60 dark:text-blue-300 rounded-full mb-4">
                    {{ $tshirtImage->category->name ?? 'Sem Categoria' }}
                </span>

                {{-- Product Title --}}
                <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white mb-3 tracking-tight">
                    {{ $tshirtImage->name }}
                </h1>

                {{-- Description --}}
                <p class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed mb-6">
                    {{ $tshirtImage->description ?? 'Esta magnífica t-shirt estampada com design exclusivo do nosso catálogo da FunShirt é produzida com tecidos premium de alta qualidade.' }}
                </p>

                <hr class="border-slate-100 dark:border-slate-800 mb-6">

                {{-- Price Section --}}
                <div class="mb-6 p-4 bg-slate-50 dark:bg-slate-950/50 rounded-2xl border border-slate-100 dark:border-slate-800/80">
                    <div class="flex items-baseline gap-2">
                        <span class="text-3xl font-black text-slate-900 dark:text-white" id="displayed-price">
                            €{{ number_format($tshirtImage->isPrivate() ? $priceConf->unit_price_own : $priceConf->unit_price_catalog, 2) }}
                        </span>
                        <span class="text-sm text-slate-500 dark:text-slate-400">/ unidade</span>
                    </div>
                    @if(($priceConf->qty_discount ?? 0) > 0)
                        <div class="mt-2 text-xs text-green-600 dark:text-green-400 flex items-center gap-1 font-semibold">
                            🏷️ Desconto de quantidade: compre {{ $priceConf->qty_discount }} ou mais unidades e pague apenas €{{ number_format($tshirtImage->isPrivate() ? $priceConf->unit_price_own_discount : $priceConf->unit_price_catalog_discount, 2) }} por cada!
                        </div>
                    @endif
                </div>

                {{-- Add to Cart Form --}}
                <form id="add-to-cart-form" action="{{ route('cart.add') }}" method="POST" class="space-y-6">
                    @csrf
                    <input type="hidden" name="tshirt_image_id" value="{{ $tshirtImage->id }}">
                    <input type="hidden" name="color_code" id="color-input" value="{{ $defaultColorCode }}">
                    <input type="hidden" name="size" id="size-input" value="M">

                    {{-- Color Selector --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">
                            Cor: <span class="font-normal text-slate-500 dark:text-slate-400" id="selected-color-name">{{ $defaultColorName }}</span>
                        </label>
                        <div class="flex flex-wrap gap-2.5">
                            @foreach($colors as $color)
                                <button type="button" 
                                        data-code="{{ $color->code }}" 
                                        data-name="{{ $color->name }}" 
                                        data-image="{{ asset('storage/tshirt_base/' . $color->code . '.jpg') }}"
                                        title="{{ $color->name }}"
                                        class="color-btn w-8 h-8 rounded-full border-2 focus:outline-none cursor-pointer hover:scale-110 active:scale-95 shadow transition-all duration-200 {{ $color->code === $defaultColorCode ? 'ring-2 ring-blue-600 dark:ring-blue-400 ring-offset-2 dark:ring-offset-slate-900 scale-110 border-transparent' : 'border-slate-200 dark:border-slate-700' }}"
                                        style="background-color: #{{ $color->code }}">
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Size Selector --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">
                            Tamanho:
                        </label>
                        <div class="flex gap-2">
                            @foreach(['XS', 'S', 'M', 'L', 'XL'] as $sz)
                                <button type="button" 
                                        data-size="{{ $sz }}"
                                        class="size-btn px-4 py-2 border rounded-lg text-sm font-bold focus:outline-none cursor-pointer hover:scale-[1.02] active:scale-[0.98] transition-all {{ $sz === 'M' ? 'bg-blue-600 border-blue-600 text-white hover:bg-blue-700 dark:bg-blue-500 dark:border-blue-500 dark:hover:bg-blue-600' : 'border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                                    {{ $sz }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Quantity Selector --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">
                            Quantidade:
                        </label>
                        <div class="flex items-center gap-4">
                            <div class="flex items-center border border-slate-200 dark:border-slate-700 rounded-lg overflow-hidden">
                                <button type="button" 
                                        id="btn-minus" 
                                        class="w-10 h-10 flex items-center justify-center font-black text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition select-none cursor-pointer focus:outline-none">
                                    -
                                </button>
                                <input type="number" 
                                       id="qty-display" 
                                       name="qty" 
                                       value="1" 
                                       min="1" 
                                       class="w-12 h-10 text-center font-bold text-slate-800 dark:text-slate-100 bg-transparent focus:outline-none select-none border-none pointer-events-none" 
                                       readonly>
                                <button type="button" 
                                        id="btn-plus" 
                                        class="w-10 h-10 flex items-center justify-center font-black text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition select-none cursor-pointer focus:outline-none">
                                    +
                                </button>
                            </div>

                            {{-- Subtotal preview --}}
                            <div class="flex items-center gap-2">
                                <span class="text-sm text-slate-500 dark:text-slate-400">Subtotal:</span>
                                <span id="subtotal-display" class="text-xl font-bold text-blue-600 dark:text-blue-400">€0.00</span>
                                <span id="discount-badge" class="hidden text-[10px] text-green-600 dark:text-green-400 font-extrabold bg-green-50 dark:bg-green-950/40 border border-green-200 dark:border-green-900/60 px-2 py-0.5 rounded-full uppercase tracking-wider animate-pulse">
                                    Desconto de Qty!
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Form Submit Button --}}
                    <div class="pt-4">
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-xl shadow transition-all duration-200 hover:shadow-md cursor-pointer flex items-center justify-center gap-2">
                            <span class="text-lg">🛒</span> Adicionar ao Carrinho
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const colorButtons = document.querySelectorAll('.color-btn');
    const colorInput = document.getElementById('color-input');
    const colorNameSpan = document.getElementById('selected-color-name');
    const baseTshirtImg = document.getElementById('base-tshirt');

    const sizeButtons = document.querySelectorAll('.size-btn');
    const sizeInput = document.getElementById('size-input');

    const qtyInput = document.getElementById('qty-display');
    const btnMinus = document.getElementById('btn-minus');
    const btnPlus = document.getElementById('btn-plus');

    const subtotalDisplay = document.getElementById('subtotal-display');
    const discountBadge = document.getElementById('discount-badge');

    // Fetch values safely from configuration
    const unitPrice = parseFloat('{{ $tshirtImage->isPrivate() ? ($priceConf->unit_price_own ?? 10.0) : ($priceConf->unit_price_catalog ?? 10.0) }}');
    const discountPrice = parseFloat('{{ $tshirtImage->isPrivate() ? ($priceConf->unit_price_own_discount ?? 8.5) : ($priceConf->unit_price_catalog_discount ?? 8.5) }}');
    const discountThreshold = parseInt('{{ $priceConf->qty_discount ?? 10 }}');

    function updatePrice() {
        const qty = parseInt(qtyInput.value) || 1;
        let pricePerUnit = unitPrice;
        let hasDiscount = false;

        if (discountThreshold > 0 && qty >= discountThreshold) {
            pricePerUnit = discountPrice;
            hasDiscount = true;
        }

        const subtotal = qty * pricePerUnit;
        subtotalDisplay.textContent = '€' + subtotal.toFixed(2);

        if (hasDiscount) {
            discountBadge.classList.remove('hidden');
        } else {
            discountBadge.classList.add('hidden');
        }
    }

    // Color buttons interaction
    colorButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            // Reset state
            colorButtons.forEach(b => {
                b.classList.remove('ring-2', 'ring-blue-600', 'dark:ring-blue-400', 'ring-offset-2', 'dark:ring-offset-slate-900', 'scale-110', 'border-transparent');
                b.classList.add('border-slate-200', 'dark:border-slate-700');
            });

            // Set active state on clicked
            this.classList.add('ring-2', 'ring-blue-600', 'dark:ring-blue-400', 'ring-offset-2', 'dark:ring-offset-slate-900', 'scale-110', 'border-transparent');
            this.classList.remove('border-slate-200', 'dark:border-slate-700');

            const code = this.getAttribute('data-code');
            const name = this.getAttribute('data-name');
            const imgSrc = this.getAttribute('data-image');

            colorInput.value = code;
            colorNameSpan.textContent = name;
            baseTshirtImg.src = imgSrc;
        });
    });

    // Size buttons interaction
    sizeButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            // Reset classes
            sizeButtons.forEach(b => {
                b.classList.remove('bg-blue-600', 'border-blue-600', 'text-white', 'hover:bg-blue-700', 'dark:bg-blue-500', 'dark:border-blue-500', 'dark:hover:bg-blue-600');
                b.classList.add('border-slate-200', 'dark:border-slate-700', 'text-slate-700', 'dark:text-slate-300', 'hover:bg-slate-50', 'dark:hover:bg-slate-800');
            });

            // Add classes to selected
            this.classList.add('bg-blue-600', 'border-blue-600', 'text-white', 'hover:bg-blue-700', 'dark:bg-blue-500', 'dark:border-blue-500', 'dark:hover:bg-blue-600');
            this.classList.remove('border-slate-200', 'dark:border-slate-700', 'text-slate-700', 'dark:text-slate-300', 'hover:bg-slate-50', 'dark:hover:bg-slate-800');

            const size = this.getAttribute('data-size');
            sizeInput.value = size;
        });
    });

    // Quantity decrement/increment
    btnMinus.addEventListener('click', function() {
        let val = parseInt(qtyInput.value) || 1;
        if (val > 1) {
            qtyInput.value = val - 1;
            updatePrice();
        }
    });

    btnPlus.addEventListener('click', function() {
        let val = parseInt(qtyInput.value) || 1;
        qtyInput.value = val + 1;
        updatePrice();
    });

    // Initialize display subtotal
    updatePrice();
});
</script>
@endpush
