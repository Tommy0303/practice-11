{{-- resources/views/products/edit.blade.php --}}

@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>商品編集</h1>
        <form action="{{ route('products.update', $product->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="product_name">商品名</label>
                <input type="text" name="product_name" id="product_name" class="form-control" value="{{ $product->name }}">
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
                <label for="company_name">メーカー</label>
                <input type="text" name="company_name" id="company_name" class="form-control" value="{{ $product->company->name ?? '' }}">
            </div>
            <div class="form-group">
                <label for="comment">コメント</label>
                <textarea name="comment" id="comment" class="form-control">{{ $product->comment }}</textarea>
            </div>
            <div class="form-group">
                <label for="image">商品画像</label>
                <input type="file" name="img_path" id="image" class="form-control-file">
            </div>
            <button type="submit" class="btn btn-primary">更新</button>
            <a href="{{ route('products.detail', ['product' => $product->id]) }}" class="btn btn-secondary">戻る</a>
        </form>
    </div>
@endsection
