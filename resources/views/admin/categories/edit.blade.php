@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Editar Categoria</h1>

    <form method="post" enctype="multipart/form-data" action="{{ route('admin.categories.update', $category) }}">
        @csrf
        <div><label>Nome</label><input name="name" value="{{ old('name', $category->name) }}" required></div>
        <div><label>Imagem</label><input type="file" name="image"></div>
        <button class="btn btn-primary">Guardar</button>
    </form>
</div>
@endsection
