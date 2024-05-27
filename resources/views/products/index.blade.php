@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>商品一覧</h1>
        <form id="search-form" action="{{ route('products.index') }}" method="GET" class="mb-3">
            <div class="row">
                <div class="col-md-4">
                    <input type="text" name="keyword" class="form-control" placeholder="商品名を入力" value="{{ request('keyword') }}">
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
                    <input type="number" name="price_min" class="form-control" placeholder="最低価格">
                </div>
                <div class="col-md-2">
                    <input type="number" name="price_max" class="form-control" placeholder="最高価格">
                </div>
                <div class="col-md-2">
                    <input type="number" name="stock_min" class="form-control" placeholder="最低在庫">
                </div>
                <div class="col-md-2">
                    <input type="number" name="stock_max" class="form-control" placeholder="最高在庫">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary">検索</button>
                </div>
            </div>
        </form>

        <a href="{{ route('products.create') }}" class="btn btn-primary mb-3">新規登録</a>
        <table id="fav-table" class="table">
            <thead>
                <tr>
                    <th>@sortablelink('id', 'ID')</th>
                    <th>@sortablelink('product_name', '商品名')</th>
                    <th>@sortablelink('price', '価格')</th>
                    <th>@sortablelink('stock', '在庫数')</th>
                    <th>@sortablelink('company.company_name', 'メーカー')</th>
                    <th>商品画像</th>
                    <th>アクション</th>
                </tr>
            </thead>
            <tbody id="product-list">
                @foreach($products as $product)
                    <tr id="product-{{ $product->id }}">
                        <td>{{ $product->id }}</td>
                        <td>{{ $product->product_name }}</td>
                        <td>{{ $product->price }}</td>
                        <td>{{ $product->stock }}</td>
                        <td>{{ $product->company ? $product->company->company_name : '未設定' }}</td>
                        <td><img src="{{ asset($product->img_path) }}" alt="商品画像" class="product-image"></td>
                        <td>
                            <a href="{{ route('products.detail', $product->id) }}" class="btn btn-sm btn-primary">詳細</a>
                            <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="d-inline delete-form" data-id="{{ $product->id }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger delete-button">削除</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div id="pagination-links">
            {{ $products->links() }}
        </div>
    </div>

    <script src="{{ asset('js/jquery.tablesorter.min.js') }}"></script>
<script>
$(document).ready(function() {
    console.log("Tablesorter script is loaded.");
    $("#fav-table").tablesorter();

    $('#search-form').submit(function(event) {
        event.preventDefault();
        $.ajax({
            url: "{{ route('products.index') }}",
            method: 'GET',
            data: $(this).serialize(),
            success: function(data) {
                $('#product-list').empty();
                data.products.forEach(function(product) {
                    $('#product-list').append(`
                        <tr id="product-${product.id}">
                            <td>${product.id}</td>
                            <td>${product.product_name}</td>
                            <td>${product.price}</td>
                            <td>${product.stock}</td>
                            <td>${product.company ? product.company.company_name : '未設定'}</td>
                            <td><img src="{{ asset('${product.img_path}') }}" alt="商品画像" class="product-image"></td>
                            <td>
                                <a href="/products/${product.id}" class="btn btn-sm btn-primary">詳細</a>
                                <form action="/products/${product.id}" method="POST" class="d-inline delete-form" data-id="${product.id}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger delete-button">削除</button>
                                </form>
                            </td>
                        </tr>
                    `);
                });

                $('#pagination-links').html(data.links);

                attachDeleteHandlers();
            }
        });
    });

    function attachDeleteHandlers() {
        $('.delete-form').off('submit').on('submit', function(event) {
            event.preventDefault();
            console.log("Delete form submitted");
            var form = $(this);
            var productId = form.data('id');
            if (confirm('本当に削除しますか？')) {
                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: form.serialize(),
                    success: function() {
                        $('#product-' + productId).remove();
                    }
                });
            }
        });
    }

    attachDeleteHandlers();
});
</script>

@endsection


@push('styles')
<link href="{{ asset('css/app.css') }}" rel="stylesheet">
@endpush
