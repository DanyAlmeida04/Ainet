<div class="p-4 bg-white rounded shadow w-72">
    <div class="flex items-center gap-3 mb-3">
        <img src="{{ asset('storage/photos/' . (Auth::user()->photo_url ?? 'anonymous.png')) }}" class="w-12 h-12 object-cover rounded-full">
        <div>
            <div class="font-semibold">{{ Auth::user()->name }}</div>
            <div class="text-sm text-gray-600">{{ Auth::user()->email }}</div>
        </div>
    </div>

    <div class="space-y-2">
        <a href="{{ route('profile.show') }}" class="block text-left w-full bg-blue-600 text-white px-3 py-2 rounded">Ver Perfil</a>
        <a href="{{ route('profile.show') }}#edit" class="block text-left w-full bg-gray-100 px-3 py-2 rounded">Editar Perfil</a>
        <a href="{{ route('orders.index') }}" class="block text-left w-full bg-gray-100 px-3 py-2 rounded">As Minhas Encomendas</a>
    </div>
</div>
