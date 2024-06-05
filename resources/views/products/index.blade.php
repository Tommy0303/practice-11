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
                    <th>@sortablelink('id', 'ID', $searchParams)</th>
                    <th>@sortablelink('product_name', '商品名', $searchParams)</th>
                    <th>@sortablelink('price', '価格', $searchParams)</th>
                    <th>@sortablelink('stock', '在庫数', $searchParams)</th>
                    <th>@sortablelink('company.company_name', 'メーカー', $searchParams)</th>
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

    var sortField = null, sortDirection = null;

    // ページがロードされたときに、ローカルストレージからフォームの値を取得して設定する
    if (localStorage.getItem('searchParams')) {
        var searchParams = JSON.parse(localStorage.getItem('searchParams'));
        $('#keyword').val(searchParams.keyword);
        $('#manufacturer').val(searchParams.manufacturer);
        $('#price_min').val(searchParams.price_min);
        $('#price_max').val(searchParams.price_max);
        $('#stock_min').val(searchParams.stock_min);
        $('#stock_max').val(searchParams.stock_max);

        if (searchParams.sort) sortField = searchParams.sort;
        if (searchParams.direction) sortDirection = searchParams.direction;
    }

    $("#fav-table").tablesorter().bind('sortEnd', function(event) {
        var table = $(this);
        var sort = table.get(0).config.sortList;

        if (sort.length > 0) {
            var sortItem = sort[0];
            sortField = table.find('th').eq(sortItem[0]).attr('data-column');
            sortDirection = sortItem[1] === 0 ? 'asc' : 'desc';
        }
    });

    $('#search-form').submit(function(event) {
        event.preventDefault();
        performSearch();
    });

    function performSearch() {
        var keyword = $('#keyword').val().trim();
        var searchParams = {
            keyword: keyword === '' ? '' : keyword,
            manufacturer: $('#manufacturer').val(),
            price_min: $('#price_min').val(),
            price_max: $('#price_max').val(),
            stock_min: $('#stock_min').val(),
            stock_max: $('#stock_max').val()
        };
    

        console.log("Keyword:", keyword);
        console.log("Manufacturer:", searchParams.manufacturer);
        console.log("Price_min:", searchParams.price_min);
        console.log("Price_max:", searchParams.price_max);
        console.log("Stock_min:", searchParams.stock_min);
        console.log("Stock_max:", searchParams.stock_max);



        console.log("Sending searchParams: ", searchParams);

        if (sortField) {
    searchParams.sort = sortField; // ソートフィールドが存在する場合にのみ設定
}
console.log("Sort field:", sortField);
if (sortDirection) {
    searchParams.direction = sortDirection; // ソート方向が存在する場合にのみ設定
}
        
        localStorage.setItem('searchParams', JSON.stringify(searchParams));
        
        // Ajaxリクエストの送信
        $.ajax({
            url: productsIndexUrl,
            method: 'GET',
            data: {...searchParams, sort: sortField, direction: sortDirection}, 
            success: function(data) {
                console.log("Received data: ", data);  // デバッグ用に追加
                console.log("Sort field:", sortField);
                console.log("Sort direction:", sortDirection);
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

                attachDeleteHandlers();
            },
            error: function(xhr, status, error) {
                console.error("AJAXエラー: ", status, error);
            }
        });
    };

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
