@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>商品一覧</h1>
        <form id="search-form" action="{{ route('products.index') }}" method="GET" class="mb-3">
    <div class="row">
        <div class="col-md-4">
            <input type="text" id="keyword" name="keyword" class="form-control" placeholder="商品名を入力" value="{{ isset($searchParams['keyword']) ? $searchParams['keyword'] : '' }}">
        </div>
        @if($companies->count() > 0)
            <div class="col-md-4">
                <select id="manufacturer" name="manufacturer" class="form-control">
                    <option value="">メーカーを選択</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}" {{ isset($searchParams['manufacturer']) && $searchParams['manufacturer'] == $company->id ? 'selected' : '' }}>
                            {{ $company->company_name }}
                        </option>
                    @endforeach
                </select>
            </div>
        @endif
        <div class="col-md-2">
            <input type="number" id="price_min" name="price_min" class="form-control" placeholder="最低価格" value="{{ isset($searchParams['price_min']) ? $searchParams['price_min'] : '' }}">
        </div>
        <div class="col-md-2">
            <input type="number" id="price_max" name="price_max" class="form-control" placeholder="最高価格" value="{{ isset($searchParams['price_max']) ? $searchParams['price_max'] : '' }}">
        </div>
        <div class="col-md-2">
            <input type="number" id="stock_min" name="stock_min" class="form-control" placeholder="最低在庫" value="{{ isset($searchParams['stock_min']) ? $searchParams['stock_min'] : '' }}">
        </div>
        <div class="col-md-2">
            <input type="number" id="stock_max" name="stock_max" class="form-control" placeholder="最高在庫" value="{{ isset($searchParams['stock_max']) ? $searchParams['stock_max'] : '' }}">
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
                    <th data-column="id">@sortablelink('id', 'ID')</th>
                    <th data-column="product_name">@sortablelink('product_name', '商品名')</th>
                    <th data-column="price">@sortablelink('price', '価格')</th>
                    <th data-column="stock">@sortablelink('stock', '在庫数')</th>
                    <th data-column="company_name">@sortablelink('company.company_name', 'メーカー')</th>
                    <th>商品画像</th>
                    <th>アクション</th>
                </tr>
            </thead>
            <tbody id="product-list">
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
            {{ $products->appends($searchParams)->links() }}
        </div>
    </div>

    <script src="{{ asset('js/jquery.tablesorter.min.js') }}"></script>
<script>
$(document).ready(function() {
    console.log("Tablesorter script is loaded.");

    // ページがロードされたときに、ローカルストレージからフォームの値を取得して設定する
    if (localStorage.getItem('searchParams')) {
        var searchParams = JSON.parse(localStorage.getItem('searchParams'));
        $('#keyword').val(searchParams.keyword);
        $('#manufacturer').val(searchParams.manufacturer);
        $('#price_min').val(searchParams.price_min);
        $('#price_max').val(searchParams.price_max);
        $('#stock_min').val(searchParams.stock_min);
        $('#stock_max').val(searchParams.stock_max);
        $('#sort').val(searchParams.sort);
        $('#direction').val(searchParams.direction);
    }

    $('#search-form').on('submit', function(e) {
        e.preventDefault();
        var params = $(this).serialize();
        fetchProducts(params);
    });

    // ソートリンクのクリックイベントをハンドル
    $('.sort-link').on('click', function(e) {
        e.preventDefault();
        var params = $('#search-form').serialize();
        params += '&sort=' + $(this).data('sort');
        params += '&direction=' + $(this).data('direction');
        fetchProducts(params);
    });

        // Ajaxリクエストの送信
        $.ajax({
            url: "/products",
            method: 'GET',
            data: params,
            success: function(data) {
                console.log("Received data: ", data);  // デバッグ用に追加
                $('#product-list').empty();
                data.products.forEach(function(product) {
                    $('#product-list').append(`
                        <tr id="product-${product.id}">
                            <td>${product.id}</td>
                            <td>${product.product_name}</td>
                            <td>${product.price}</td>
                            <td>${product.stock}</td>
                            <td>${product.company ? product.company.company_name : '未設定'}</td>
                            <td><img src="${product.img_path}" alt="商品画像" class="product-image"></td>
                            <td>
                                <a href="${productDetailUrlTemplate.replace(':id', product.id)}" class="btn btn-sm btn-primary">詳細</a>
                                <form action="${productDeleteUrlTemplate.replace(':id', product.id)}" method="POST" class="d-inline delete-form" data-id="${product.id}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger delete-button">削除</button>
                                </form>
                            </td>
                        </tr>
                        
                    `);
                });

                $('#pagination-links').html(data.links);

                // テーブルの更新
                $("#fav-table").trigger("update");

                attachDeleteHandlers();
            },
            error: function(xhr, status, error) {
                console.error("AJAXエラー: ", status, error);
            }
        });
    }
    fetchProducts($('#search-form').serialize());

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
                        $("#product-table").trigger("update"); // テーブルの更新
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
