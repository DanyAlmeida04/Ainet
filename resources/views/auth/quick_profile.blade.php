<div class="bg-white rounded shadow p-3 w-64">
    <div class="flex items-center space-x-3">
        <img src="{{ asset('storage/photos/' . (Auth::user()->photo_url ?? 'anonymous.png')) }}" alt="Avatar" class="w-10 h-10 rounded-full">
        <div>
            <div class="font-bold">{{ Auth::user()->name }}</div>
            <div class="text-xs text-gray-500">{{ Auth::user()->email }}</div>
        </div>
    </div>
    <div class="mt-3 space-y-1.5 border-t border-slate-100 dark:border-slate-800 pt-2.5">
        <a href="{{ route('profile.show') }}" class="block text-sm font-semibold text-slate-700 hover:text-blue-600 dark:text-slate-300 dark:hover:text-blue-400">Meu Perfil</a>
        @if(Auth::user()->user_type === 'C')
            <a href="{{ route('profile.images.index') }}" class="block text-sm font-semibold text-slate-700 hover:text-blue-600 dark:text-slate-300 dark:hover:text-blue-400">As Minhas Imagens</a>
            <a href="{{ route('orders.index') }}" class="block text-sm font-semibold text-slate-700 hover:text-blue-600 dark:text-slate-300 dark:hover:text-blue-400">Histórico de Encomendas</a>
        @endif
        @can('manage-users')
            <a href="{{ route('admin.dashboard') }}" class="block text-sm font-semibold text-slate-700 hover:text-blue-600 dark:text-slate-300 dark:hover:text-blue-400">Painel Administrativo</a>
        @endcan
        @can('process-orders')
            <a href="{{ route('employee.orders.index') }}" class="block text-sm font-semibold text-slate-700 hover:text-blue-600 dark:text-slate-300 dark:hover:text-blue-400">Área do Funcionário</a>
        @endcan
    </div>
</div>
