@extends('layouts.admin')

@section('admin-content')
<div class="mt-4 max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.designs.index') }}" class="text-xs font-semibold text-slate-500 hover:text-blue-600 transition flex items-center gap-1 hover:no-underline">
            &larr; Voltar à Lista
        </a>
        <h2 class="text-xl font-bold text-slate-800 dark:text-white mt-2">Adicionar Novo Design ao Catálogo</h2>
    </div>

    @if ($errors->any())
        <div class="mb-6 p-4 bg-rose-50 dark:bg-rose-950/20 text-rose-800 dark:text-rose-350 rounded-xl border border-rose-250/50 dark:border-rose-900/60 shadow-sm text-xs space-y-1">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
        <form action="{{ route('admin.designs.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Nome do Design *</label>
                <input type="text" name="name" value="{{ old('name') }}" placeholder="Ex: Caveira Retro, Sunset Beach..." required
                       class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-900 dark:text-slate-200">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Descrição</label>
                <textarea name="description" placeholder="Uma breve descrição sobre este design..." rows="4"
                          class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-900 dark:text-slate-200">{{ old('description') }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Categoria</label>
                <select name="category_id" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-900 dark:text-slate-200">
                    <option value="">Sem Categoria (Nenhuma)</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Ficheiro de Imagem *</label>
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-slate-300 dark:border-slate-700 border-dashed rounded-xl hover:border-slate-400 dark:hover:border-slate-500 transition duration-150 relative">
                    <div class="space-y-1 text-center" id="uploadPlaceholder">
                        <svg class="mx-auto h-10 w-10 text-slate-450" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div class="flex text-xs text-slate-600 dark:text-slate-400 justify-center">
                            <label for="image-upload" class="relative cursor-pointer bg-white dark:bg-slate-900 rounded-md font-semibold text-blue-650 hover:text-blue-500 focus-within:outline-none">
                                <span>Carregar ficheiro</span>
                                <input id="image-upload" name="image" type="file" required class="sr-only" accept="image/*">
                            </label>
                            <p class="pl-1">ou arrastar e soltar</p>
                        </div>
                        <p class="text-[10px] text-slate-500">PNG, JPG, WEBP até 2MB</p>
                    </div>
                    
                    {{-- Selected Image Preview --}}
                    <div id="imagePreviewContainer" class="hidden flex flex-col items-center gap-2">
                        <img id="imgPreview" src="" class="max-h-40 max-w-full object-contain rounded border shadow-sm">
                        <button type="button" id="removeImgBtn" class="text-[10px] text-rose-600 hover:text-rose-700 font-bold focus:outline-none cursor-pointer">
                            Remover Imagem
                        </button>
                    </div>
                </div>
            </div>

            {{-- Notify Customers Checkbox --}}
            <div class="flex items-center gap-2.5 p-3.5 bg-slate-50 dark:bg-slate-800/40 rounded-xl border border-slate-100 dark:border-slate-850">
                <input type="checkbox" name="notify_customers" id="notify_customers" value="1" {{ old('notify_customers') ? 'checked' : '' }}
                       class="w-4 h-4 rounded text-blue-600 border-slate-300 focus:ring-blue-500 cursor-pointer">
                <label for="notify_customers" class="text-xs font-semibold text-slate-750 dark:text-slate-300 cursor-pointer select-none">
                    Enviar e-mail aos clientes a divulgar este novo design (Newsletter)
                </label>
            </div>

            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-3">
                <a href="{{ route('admin.designs.index') }}" class="px-4 py-2 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition hover:no-underline">
                    Cancelar
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold shadow transition cursor-pointer">
                    Criar Design
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileSelector = document.getElementById('image-upload');
    const uploadPlaceholder = document.getElementById('uploadPlaceholder');
    const previewContainer = document.getElementById('imagePreviewContainer');
    const imgPreview = document.getElementById('imgPreview');
    const removeImgBtn = document.getElementById('removeImgBtn');

    fileSelector.addEventListener('change', function(e) {
        const files = e.target.files;
        if (files && files.length > 0) {
            const file = files[0];
            const reader = new FileReader();
            reader.onload = function(e) {
                imgPreview.src = e.target.result;
                uploadPlaceholder.classList.add('hidden');
                previewContainer.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }
    });

    removeImgBtn.addEventListener('click', function() {
        fileSelector.value = '';
        imgPreview.src = '';
        previewContainer.classList.add('hidden');
        uploadPlaceholder.classList.remove('hidden');
    });
});
</script>
@endpush
