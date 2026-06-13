@extends('layouts.admin')

@section('admin-content')
<div class="max-w-2xl mx-auto bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200/60 dark:border-slate-800 p-6 sm:p-8">
    <div class="flex items-center justify-between mb-6 border-b border-slate-100 dark:border-slate-800 pb-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800 dark:text-white">Editar Utilizador</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Atualize as informações de perfil e permissões de {{ $user->name }}.</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="text-sm font-medium text-slate-500 hover:text-blue-600 hover:underline dark:text-slate-400 dark:hover:text-blue-400 transition-colors">
            Cancelar
        </a>
    </div>

    <form method="post" action="{{ route('admin.users.update', $user) }}" class="space-y-6">
        @csrf
        
        <div>
            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Nome Completo</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                   class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-900 dark:text-slate-250">
            @error('name')
                <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Endereço de Email</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                   class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-900 dark:text-slate-250">
            @error('email')
                <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Tipo de Utilizador</label>
            <select name="user_type" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-900 dark:text-slate-250">
                <option value="C" {{ old('user_type', $user->user_type) == 'C' ? 'selected' : '' }}>Cliente</option>
                <option value="E" {{ old('user_type', $user->user_type) == 'E' ? 'selected' : '' }}>Funcionário</option>
                <option value="A" {{ old('user_type', $user->user_type) == 'A' ? 'selected' : '' }}>Administrador</option>
            </select>
            @error('user_type')
                <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center gap-3 bg-slate-50 dark:bg-slate-800/30 p-4 rounded-xl border border-slate-200/60 dark:border-slate-800">
            <input type="checkbox" id="blocked" name="blocked" value="1" {{ old('blocked', $user->blocked) ? 'checked' : '' }}
                   class="h-4.5 w-4.5 rounded border-slate-300 dark:border-slate-700 text-blue-600 focus:ring-blue-500">
            <label for="blocked" class="text-sm font-semibold text-slate-700 dark:text-slate-300 select-none cursor-pointer">
                Bloquear conta do utilizador (impede o login)
            </label>
        </div>

        <div class="flex items-center gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
            <button type="submit" class="bg-blue-600 text-white font-semibold py-2.5 px-6 rounded-xl text-sm hover:bg-blue-700 hover:shadow-md transition duration-150 cursor-pointer shadow-sm">
                Guardar Alterações
            </button>
            <a href="{{ route('admin.users.index') }}" class="bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-semibold py-2.5 px-6 rounded-xl text-sm hover:bg-slate-200 dark:hover:bg-slate-700 transition text-center">
                Cancelar
            </a>
        </div>
    </form>
</div>
@endsection
