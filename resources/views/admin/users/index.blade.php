@extends('layouts.admin')

@section('admin-content')
<div class="bg-white dark:bg-slate-900 rounded-2xl shadow border border-slate-200/60 dark:border-slate-800 p-6">
    <h2 class="text-xl font-bold text-slate-800 dark:text-white mb-6">Gestão de Utilizadores</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Email</th>
                <th>Tipo</th>
                <th>Blocked</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $u)
            <tr>
                <td>{{ $u->id }}</td>
                <td>{{ $u->name }}</td>
                <td>{{ $u->email }}</td>
                <td>{{ $u->user_type }}</td>
                <td>{{ $u->blocked ? 'Sim' : 'Não' }}</td>
                <td>
                    <a href="{{ route('admin.users.edit', $u) }}" class="btn btn-sm btn-primary">Editar</a>
                    <form action="{{ route('admin.users.toggleBlock', $u) }}" method="post" style="display:inline">
                        @csrf
                        <button class="btn btn-sm btn-warning">{{ $u->blocked ? 'Desbloquear' : 'Bloquear' }}</button>
                    </form>
                    <form action="{{ route('admin.users.destroy', $u) }}" method="post" style="display:inline" onsubmit="return confirm('Tem a certeza?');">
                        @csrf
                        <button class="btn btn-sm btn-danger">Eliminar</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $users->links() }}
</div>
@endsection
