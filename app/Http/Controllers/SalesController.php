<?php

namespace App\Http\Controllers;

use App\Models\Sales;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        // 商品が存在しない場合の処理
        if (!$product) {
            return response()->json(['error' => '該当の商品が見つかりません'], 404);
        }

        // 在庫が不足している場合の処理
        if ($product->stock < $data['quantity']) {
            return response()->json(['error' => '在庫が不足しています'], 400);
        }

        // トランザクションを使用して購入処理
        DB::transaction(function () use ($product, $data) {
            // salesテーブルにデータを保存
            $sale = new Sales();
            $sale->product_id = $product->id;
            $sale->quantity = $data['quantity'];
            $sale->price = $product->price;
            $sale->total = $product->price * $data['quantity'];
            $sale->save();

            // productsテーブルの在庫を減らす
            $product->stock -= $data['quantity'];
            $product->save();
        });

        // 成功した場合はJSONレスポンスを返す
        return response()->json(['message' => '購入が完了しました'], 200);
    }
}
