@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>商品一覧</h1>
        <form action="{{ route('products.index') }}" method="GET" class="mb-3">
            <div class="row">
                <div class="col-md-4">
                    <input type="text" name="keyword" class="form-control" placeholder="商品名を入力">
                </div>
                @if($companies->count() > 0)
                    <div class="col-md-4">
                        <select name="manufacturer" class="form-control">
                            <option value="">メーカーを選択</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}">{{  $company->company_name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary">検索</button>
                </div>
            </div>
        </form>

        <a href="{{ route('products.create') }}" class="btn btn-primary mb-3">新規登録</a>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>商品名</th>
                    <th>価格</th>
                    <th>在庫数</th>
                    <th>メーカー</th>
                    <th>商品画像</th>
                    <th>アクション</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $product)
                    <tr>
                        <td>{{ $product->id }}</td>
                        <td>{{ $product->product_name }}</td>
                        <td>{{ $product->price }}</td>
                        <td>{{ $product->stock }}</td>
                        <td>{{ $product->company ? $product->company->company_name : '未設定' }}</td>
                        <td><img src="{{ asset($product->img_path) }}" alt="商品画像" class="product-image"></td>
                        <td>
                            <a href="{{ route('products.detail', $product->id) }}" class="btn btn-sm btn-primary">詳細</a>
                            <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('本当に削除しますか？')">削除</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection

@push('styles')
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
@endpush
