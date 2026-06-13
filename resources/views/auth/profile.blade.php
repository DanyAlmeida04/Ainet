@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto my-12 bg-white p-8 rounded-lg shadow">
    <h2 class="text-2xl font-bold mb-6 text-center text-gray-800">Perfil</h2>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4 text-sm">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-sm">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <div class="grid grid-cols-2 gap-6">
        <div>
            <form action="{{ route('profile.update') }}" method="POST" class="space-y-4">
                @csrf
                
                {{-- Profile Photo Upload with Crop --}}
                <div class="flex flex-col items-center gap-3 mb-6 bg-slate-50 dark:bg-slate-900/40 p-4 rounded-2xl border border-slate-100 dark:border-slate-800/80">
                    <label class="block text-sm font-bold text-gray-700 mb-1">Foto de Perfil</label>
                    <div class="relative group cursor-pointer" id="avatarClickArea" title="Clique para alterar a foto de perfil">
                        <img id="avatarPreview" 
                             src="{{ asset('storage/photos/' . ($user->photo_url ?? 'anonymous.png')) }}" 
                             alt="Avatar" 
                             class="w-28 h-28 rounded-full object-cover border-4 border-white dark:border-slate-800 shadow-md group-hover:opacity-80 transition duration-150">
                        <div class="absolute inset-0 flex items-center justify-center bg-black/40 text-white text-[10px] font-bold rounded-full opacity-0 group-hover:opacity-100 transition duration-150">
                            Alterar Foto
                        </div>
                    </div>
                    
                    <button type="button" id="uploadPhotoBtn" class="text-xs font-bold text-blue-600 hover:text-blue-700 dark:text-blue-400 cursor-pointer">
                        Carregar nova imagem
                    </button>
                    
                    <input type="file" id="avatarFileSelector" accept="image/*" class="hidden">
                    <input type="hidden" name="photo_base64" id="photoBase64">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Nome Completo *</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                           class="mt-1 w-full border border-gray-300 rounded px-3 py-2">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">NIF</label>
                    <input type="text" name="nif" value="{{ old('nif', $user->customer->nif ?? '') }}"
                           class="mt-1 w-full border border-gray-300 rounded px-3 py-2">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Morada</label>
                    <input type="text" name="address" value="{{ old('address', $user->customer->address ?? '') }}"
                           class="mt-1 w-full border border-gray-300 rounded px-3 py-2">
                </div>

                <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded font-semibold hover:bg-blue-700">
                    Atualizar Perfil
                </button>
            </form>

            <p class="text-sm text-center text-gray-600 mt-4">
                <a href="{{ route('password.show') }}" class="text-blue-600 hover:underline">Alterar Password</a>
            </p>
        </div>

        <div>
            <div class="bg-gray-50 p-4 rounded">
                <h3 class="font-bold">Resumo</h3>
                @php
                    $ordersCount = $user->customer ? $user->customer->orders()->count() : 0;
                    $totalSpent = $user->customer ? $user->customer->orders()->where('status','closed')->sum('total_price') : 0;
                    $lastOrder = $user->customer ? $user->customer->orders()->orderBy('date','desc')->first() : null;
                @endphp

                <p>Encomendas: <strong>{{ $ordersCount }}</strong></p>
                <p>Total gasto (fechadas): <strong>€ {{ number_format($totalSpent,2) }}</strong></p>
                @if($lastOrder)
                    <p>Última encomenda: #{{ $lastOrder->id }} em {{ $lastOrder->date->format('Y-m-d') }}</p>
                @endif
            </div>

            <div class="mt-4 bg-white p-4 rounded shadow">
                <h3 class="font-bold mb-2">Últimas Encomendas</h3>
                @php
                    $recent = $user->customer ? $user->customer->orders()->orderBy('date','desc')->limit(5)->get() : collect();
                @endphp
                @foreach($recent as $o)
                    <div class="border-b py-2">
                        <div class="flex items-center">
                            <div>#{{ $o->id }} - {{ $o->date->format('Y-m-d') }}</div>
                            <div class="ml-auto">€ {{ number_format($o->total_price,2) }}</div>
                        </div>
                        <div class="text-sm text-gray-600">Status: {{ $o->status }}</div>
                    </div>
                @endforeach

                <p class="mt-3 text-right"><a href="{{ route('orders.index') }}" class="text-sm text-blue-600">Ver histórico completo</a></p>
            </div>
        </div>
    </div>
</div>

{{-- Cropper Modal Overlay --}}
<div id="cropperModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm hidden">
    <div class="bg-white dark:bg-slate-900 w-full max-w-lg rounded-2xl shadow-xl overflow-hidden m-4 border border-slate-200 dark:border-slate-800">
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <h3 class="text-base font-bold text-slate-850 dark:text-white">Recortar Foto de Perfil</h3>
            <button type="button" id="closeCropperModal" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-250 font-bold text-xl cursor-pointer focus:outline-none">&times;</button>
        </div>
        <div class="p-6">
            <div class="max-h-[340px] overflow-hidden bg-slate-100 dark:bg-slate-950 rounded-xl flex items-center justify-center">
                <img id="imageToCrop" src="" class="max-w-full max-h-[300px]">
            </div>
            <p class="text-[11px] text-slate-400 mt-3 text-center">Arraste a caixa de seleção para focar a área desejada da foto (formato quadrado).</p>
        </div>
        <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/20 flex justify-end gap-2.5">
            <button type="button" id="cancelCropBtn" class="px-4 py-2 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer">Cancelar</button>
            <button type="button" id="applyCropBtn" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold shadow transition cursor-pointer">Cortar e Aplicar</button>
        </div>
    </div>
</div>

@push('scripts')
<!-- Cropper.js CSS & JS CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const avatarClickArea = document.getElementById('avatarClickArea');
    const uploadPhotoBtn = document.getElementById('uploadPhotoBtn');
    const avatarFileSelector = document.getElementById('avatarFileSelector');
    const avatarPreview = document.getElementById('avatarPreview');
    const photoBase64 = document.getElementById('photoBase64');

    const cropperModal = document.getElementById('cropperModal');
    const imageToCrop = document.getElementById('imageToCrop');
    const closeCropperModal = document.getElementById('closeCropperModal');
    const cancelCropBtn = document.getElementById('cancelCropBtn');
    const applyCropBtn = document.getElementById('applyCropBtn');

    let cropper = null;

    function openModal(imageSrc) {
        imageToCrop.src = imageSrc;
        cropperModal.classList.remove('hidden');

        if (cropper) {
            cropper.destroy();
        }

        // Initialize Cropper.js with 1:1 ratio
        cropper = new Cropper(imageToCrop, {
            aspectRatio: 1,
            viewMode: 1,
            dragMode: 'move',
            autoCropArea: 0.9,
            restore: false,
            guides: true,
            center: true,
            highlight: false,
            cropBoxMovable: true,
            cropBoxResizable: true,
            toggleDragModeOnDblclick: false
        });
    }

    function closeModal() {
        cropperModal.classList.add('hidden');
        if (cropper) {
            cropper.destroy();
            cropper = null;
        }
        avatarFileSelector.value = ''; 
    }

    const triggerSelect = () => avatarFileSelector.click();
    if (avatarClickArea) avatarClickArea.addEventListener('click', triggerSelect);
    if (uploadPhotoBtn) uploadPhotoBtn.addEventListener('click', triggerSelect);

    avatarFileSelector.addEventListener('change', function(e) {
        const files = e.target.files;
        if (files && files.length > 0) {
            const file = files[0];
            const reader = new FileReader();
            reader.onload = function(e) {
                openModal(e.target.result);
            };
            reader.readAsDataURL(file);
        }
    });

    closeCropperModal.addEventListener('click', closeModal);
    cancelCropBtn.addEventListener('click', closeModal);

    applyCropBtn.addEventListener('click', function() {
        if (cropper) {
            const canvas = cropper.getCroppedCanvas({
                width: 256,
                height: 256
            });
            const croppedDataUrl = canvas.toDataURL('image/jpeg');
            
            // Show preview
            avatarPreview.src = croppedDataUrl;
            
            // Populate hidden input
            photoBase64.value = croppedDataUrl;
            
            closeModal();
        }
    });
});
</script>
@endpush
@endsection
