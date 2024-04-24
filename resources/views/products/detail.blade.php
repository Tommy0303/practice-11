{{-- resources/views/products/detail.blade.php --}}

@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">商品詳細</div>

                    <div class="card-body">
                        <p>ID: {{ $product->product_id }}</p>
                        <p>商品名: {{ $product->product_name }}</p>
                        <p>価格: {{ $product->price }}</p>
                        <p>在庫数: {{ $product->stock }}</p>
                        <p>メーカー: {{ $product->company ? $product->company->company_name : '未設定' }}</p>
                        <p>コメント: {{ $product->comment }}</p>
                        <img src="{{ asset($product->img_path) }}" alt="商品画像" class="product-image">
                        <a href="{{ route('products.edit', ['product' => $product->id]) }}" class="btn btn-primary">編集</a>

                        <a href="{{ route('products.index') }}" class="btn btn-primary">戻る</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
 @endsection

 @push('styles')
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
@endpush