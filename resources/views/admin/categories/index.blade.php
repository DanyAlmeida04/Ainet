@extends('layouts.admin')

@section('admin-content')
<div class="bg-white dark:bg-slate-900 rounded-2xl shadow border border-slate-200/60 dark:border-slate-800 p-6">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-bold text-slate-800 dark:text-white">Categorias</h2>
        <a href="{{ route('admin.categories.create') }}" class="inline-flex items-center gap-1.5 bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700 transition">+ Nova Categoria</a>
    </div>

    <table class="table">
        <thead>
            <tr><th>ID</th><th>Nome</th><th>Ações</th></tr>
        </thead>
        <tbody>
        @foreach($categories as $c)
            <tr>
                <td>{{ $c->id }}</td>
                <td>{{ $c->name }}</td>
                <td>
                    <a href="{{ route('admin.categories.edit', $c) }}" class="btn btn-sm btn-primary">Editar</a>
                    <form action="{{ route('admin.categories.destroy', $c) }}" method="post" style="display:inline">@csrf<button class="btn btn-sm btn-danger">Eliminar</button></form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $categories->links() }}
</div>
@endsection
