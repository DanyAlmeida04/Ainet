@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Editar Utilizador</h1>

    <form method="post" action="{{ route('admin.users.update', $user) }}">
        @csrf
        <div class="mb-3">
            <label class="form-label">Nome</label>
            <input class="form-control" name="name" value="{{ old('name', $user->name) }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input class="form-control" name="email" value="{{ old('email', $user->email) }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Tipo</label>
            <select name="user_type" class="form-control">
                <option value="C" {{ $user->user_type=='C'?'selected':'' }}>Cliente</option>
                <option value="E" {{ $user->user_type=='E'?'selected':'' }}>Funcionário</option>
                <option value="A" {{ $user->user_type=='A'?'selected':'' }}>Administrador</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Blocked</label>
            <input type="checkbox" name="blocked" value="1" {{ $user->blocked ? 'checked' : '' }}>
        </div>

        <button class="btn btn-primary">Guardar</button>
    </form>
</div>
@endsection
