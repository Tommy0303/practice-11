{{-- resources/views/products/edit.blade.php --}}

@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>商品編集</h1>
        <form action="{{ route('products.update', $product->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="name">商品名</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ $product->name }}">
            </div>
            <div class="form-group">
                <label for="price">価格</label>
                <input type="number" name="price" id="price" class="form-control" value="{{ $product->price }}">
            </div>
            <div class="form-group">
                <label for="stock">在庫数</label>
                <input type="number" name="stock" id="stock" class="form-control" value="{{ $product->stock }}">
            </div>
            <div class="form-group">
                <label for="manufacturer">メーカー</label>
                <input type="text" name="manufacturer" id="manufacturer" class="form-control" value="{{ $product->manufacturer }}">
            </div>
            <button type="submit" class="btn btn-primary">更新</button>
            <a href="{{ route('products.index') }}" class="btn btn-secondary">戻る</a>
        </form>
    </div>
@endsection
