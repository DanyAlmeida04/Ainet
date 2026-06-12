@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Categorias</h1>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">Criar</a>

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
