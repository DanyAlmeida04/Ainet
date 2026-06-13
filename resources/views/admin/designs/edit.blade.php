@extends('layouts.admin')

@section('admin-content')
<div class="mt-4 max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.designs.index') }}" class="text-xs font-semibold text-slate-500 hover:text-blue-600 transition flex items-center gap-1 hover:no-underline">
            &larr; Voltar à Lista
        </a>
        <h2 class="text-xl font-bold text-slate-800 dark:text-white mt-2">Editar Design do Catálogo</h2>
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
        <form action="{{ route('admin.designs.update', $tshirtImage) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Nome do Design *</label>
                <input type="text" name="name" value="{{ old('name', $tshirtImage->name) }}" required
                       class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Descrição</label>
                <textarea name="description" placeholder="Uma breve descrição sobre este design..." rows="4"
                          class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description', $tshirtImage->description) }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Categoria</label>
                <select name="category_id" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Sem Categoria (Nenhuma)</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id', $tshirtImage->category_id) == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Substituir Ficheiro de Imagem (Opcional)</label>
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-slate-300 dark:border-slate-700 border-dashed rounded-xl hover:border-slate-400 dark:hover:border-slate-500 transition duration-150 relative">
                    
                    {{-- Current Design Image --}}
                    <div class="flex flex-col items-center gap-2" id="currentImageContainer">
                        <span class="text-[10px] text-slate-400 font-semibold mb-1">Imagem Atual:</span>
                        <img id="imgPreview" src="{{ asset('storage/tshirt_images/' . $tshirtImage->image_url) }}" class="max-h-40 max-w-full object-contain rounded border shadow-sm">
                        
                        <div class="flex text-xs text-slate-600 dark:text-slate-400 justify-center mt-2">
                            <label for="image-upload" class="relative cursor-pointer bg-white dark:bg-slate-900 rounded-md font-semibold text-blue-650 hover:text-blue-500 focus-within:outline-none">
                                <span>Alterar Ficheiro</span>
                                <input id="image-upload" name="image" type="file" class="sr-only" accept="image/*">
                            </label>
                        </div>
                        <p class="text-[10px] text-slate-500">PNG, JPG, WEBP até 2MB</p>
                    </div>
                    
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-3">
                <a href="{{ route('admin.designs.index') }}" class="px-4 py-2 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition hover:no-underline">
                    Cancelar
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold shadow transition cursor-pointer">
                    Guardar Alterações
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
    const imgPreview = document.getElementById('imgPreview');

    fileSelector.addEventListener('change', function(e) {
        const files = e.target.files;
        if (files && files.length > 0) {
            const file = files[0];
            const reader = new FileReader();
            reader.onload = function(e) {
                imgPreview.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });
});
</script>
@endpush
