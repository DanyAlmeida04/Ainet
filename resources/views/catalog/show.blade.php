@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="relative">
                <img id="tshirt-base" src="{{ asset('storage/tshirt_base/plain_white.png') }}" alt="T-shirt Base" class="w-full rounded-lg shadow-md">
                <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-1/3">
                    <img src="{{ asset('storage/tshirt_images/' . $tshirtImage->image_url) }}" alt="{{ $tshirtImage->name }}" class="w-full h-auto">
                </div>
            </div>
            <div>
                <h1 class="text-4xl font-bold mb-4">{{ $tshirtImage->name }}</h1>
                <p class="text-gray-700 text-lg mb-6">{{ $tshirtImage->description }}</p>

                <div class="bg-gray-100 p-6 rounded-lg">
                    <form action="{{ route('cart.add') }}" method="POST">
                        @csrf
                        <input type="hidden" name="tshirt_image_id" value="{{ $tshirtImage->id }}">
                        <input type="hidden" name="color_code" id="selected-color" value="#FFFFFF">

                        <div class="mb-4">
                            <label class="block text-gray-700 font-semibold mb-2">Cor:</label>
                            <div class="flex flex-wrap gap-2">
                                @foreach($colors as $color)
                                    <div
                                        class="color-swatch w-8 h-8 rounded-full cursor-pointer border-2 border-transparent transition-transform duration-150 transform hover:scale-110"
                                        style="background-color: {{ $color->code }};"
                                        data-color-code="{{ $color->code }}"
                                        title="{{ $color->name }}"
                                    ></div>
                                @endforeach
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="size" class="block text-gray-700 font-semibold mb-2">Tamanho:</label>
                            <select name="size" id="size" class="w-full border-gray-300 rounded-md shadow-sm">
                                <option value="XS">XS</option>
                                <option value="S">S</option>
                                <option value="M">M</option>
                                <option value="L">L</option>
                                <option value="XL">XL</option>
                            </select>
                        </div>

                        <div class="mb-6">
                            <label for="qty" class="block text-gray-700 font-semibold mb-2">Quantidade:</label>
                            <input type="number" name="qty" id="qty" value="1" min="1" class="w-full border-gray-300 rounded-md shadow-sm">
                        </div>

                        <button type="submit" class="w-full bg-blue-600 text-white font-bold py-3 px-6 rounded-md hover:bg-blue-700">Adicionar ao Carrinho</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="mt-12">
            <a href="{{ route('catalog.index') }}" class="text-blue-600 hover:underline">&larr; Voltar ao Catálogo</a>
        </div>
    </div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const swatches = document.querySelectorAll('.color-swatch');
        const tshirtBase = document.getElementById('tshirt-base');
        const selectedColorInput = document.getElementById('selected-color');

        function selectSwatch(swatch) {
            if (!swatch) return;

            // Update visual state
            swatches.forEach(s => {
                s.classList.remove('border-blue-500', 'scale-110');
                s.classList.add('border-transparent');
            });
            swatch.classList.remove('border-transparent');
            swatch.classList.add('border-blue-500', 'scale-110');

            // Update hidden input
            selectedColorInput.value = swatch.dataset.colorCode;
        }

        // --- Initial State ---
        // Find the "white" swatch and select it by default.
        let initialSwatch = Array.from(swatches).find(s => s.title.toLowerCase() === 'white' || s.title.toLowerCase() === 'branco');
        if (initialSwatch) {
            selectSwatch(initialSwatch);
            tshirtBase.src = "{{ asset('storage/tshirt_base/plain_white.png') }}";
            selectedColorInput.value = initialSwatch.dataset.colorCode;
        }

        // --- Event Listener ---
        swatches.forEach(swatch => {
            swatch.addEventListener('click', function() {
                const colorCode = this.dataset.colorCode;
                const colorName = this.title.toLowerCase();
                let imageUrl;

                if (colorName === 'white' || colorName === 'branco') {
                    imageUrl = "{{ asset('storage/tshirt_base/plain_white.png') }}";
                } else {
                    const filename = colorCode.substring(1);
                    imageUrl = `{{ asset('storage/tshirt_base') }}/${filename}.jpg`;
                }

                tshirtBase.src = imageUrl;
                selectSwatch(this);
            });
        });
    });
</script>
@endpush
@endsection
