<?php

namespace App\Http\Controllers;

use App\Models\Sales;
use App\Models\Product;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // フォームから送られてきたデータを取得
        $data = $request->all();

        // productsテーブルからidが一致する商品を取得
        $product = Product::find($data['product_id']);

        // salesテーブルにデータを保存
        $sale = new Sales();
        $sale->product_id = $product->id;
        // その他の必要なフィールドを追加

        $sale->save();

        // 成功した場合はリダイレクトなどの処理を行う
    }
}
