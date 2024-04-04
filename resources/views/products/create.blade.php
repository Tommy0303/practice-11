{{-- resources/views/products/create.blade.php --}}

@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>商品登録</h1>
        <form action="{{ route('products.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="product_name">商品名</label>
                <input type="text" name="product_name" id="product_name" class="form-control">
            </div>
            <div class="form-group">
                <label for="price">価格</label>
                <input type="number" name="price" id="price" class="form-control">
            </div>
            <div class="form-group">
                <label for="stock">在庫数</label>
                <input type="number" name="stock" id="stock" class="form-control">
            </div>
            <div class="form-group">
                <label for="company_name">メーカー</label>
                <input type="text" name="company_name" id="company_name" class="form-control">
            </div>
            <div class="form-group">
                <label for="comment">コメント</label>
                <textarea name="comment" id="comment" class="form-control" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label for="image">商品画像</label>
                <input type="file" name="img_path" id="image" class="form-control-file">
            </div>
            <button type="submit" class="btn btn-primary">登録</button>
            <a href="{{ route('products.index') }}" class="btn btn-secondary">戻る</a>
        </form>
    </div>
@endsection
