@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Criar Categoria</h1>

    <form method="post" enctype="multipart/form-data" action="{{ route('admin.categories.store') }}">
        @csrf
        <div><label>Nome</label><input name="name" required></div>
        <div><label>Imagem</label><input type="file" name="image"></div>
        <button class="btn btn-primary">Criar</button>
    </form>
</div>
@endsection
