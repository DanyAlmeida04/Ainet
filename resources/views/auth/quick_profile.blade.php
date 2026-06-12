<div class="bg-white rounded shadow p-3 w-64">
    <div class="flex items-center space-x-3">
        <img src="{{ asset('storage/photos/' . (Auth::user()->photo_url ?? 'anonymous.png')) }}" alt="Avatar" class="w-10 h-10 rounded-full">
        <div>
            <div class="font-bold">{{ Auth::user()->name }}</div>
            <div class="text-xs text-gray-500">{{ Auth::user()->email }}</div>
        </div>
    </div>
    <div class="mt-3">
        <a href="{{ route('profile.show') }}" class="block text-sm">Perfil</a>
        @can('manage-users')
            <a href="{{ route('admin.dashboard') }}" class="block text-sm">Admin</a>
        @endcan
        @can('process-orders')
            <a href="{{ route('admin.orders.index') }}" class="block text-sm">Área Funcionário</a>
        @endcan
    </div>
</div>
