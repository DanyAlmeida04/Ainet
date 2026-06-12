@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="relative">
                <img id="tshirt-base" src="{{ asset('storage/tshirt_colors/white.png') }}" alt="T-shirt Base" class="w-full rounded-lg shadow-md">
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
                            <div class="flex space-x-2">
                                @foreach($colors as $color)
                                    <div class="color-swatch-wrapper">
                                        <div
                                            class="color-swatch w-8 h-8 rounded-full cursor-pointer border-2 border-transparent transition-transform duration-150 transform hover:scale-110"
                                            style="background-color: {{ $color->code }};"
                                            data-color-name="{{ $color->name }}"
                                            data-color-code="{{ $color->code }}"
                                            title="{{ $color->name }}"
                                        ></div>
                                    </div>
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

        // Set initial selected state
        const initialSwatch = document.querySelector('.color-swatch[data-color-name="white"]');
        if (initialSwatch) {
            initialSwatch.classList.add('border-blue-500', 'scale-110');
            initialSwatch.classList.remove('border-transparent');
        }

        swatches.forEach(swatch => {
            swatch.addEventListener('click', function() {
                // Update hidden input
                selectedColorInput.value = this.dataset.colorCode;

                // Update T-shirt image
                const colorName = this.dataset.colorName.toLowerCase();
                tshirtBase.src = `{{ asset('storage/tshirt_colors') }}/${colorName}.png`;

                // Update selected visual state
                swatches.forEach(s => s.classList.replace('border-blue-500', 'border-transparent'));
                this.classList.replace('border-transparent', 'border-blue-500');
            });
        });
    });
</script>
@endpush
@endsection
