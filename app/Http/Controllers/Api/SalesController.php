<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Support\Facades\Validator;

class SalesController extends Controller
{
    public function purchase(Request $request)
    {
        // バリデーション
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // 商品の取得
        $product = Product::find($request->product_id);

        // 在庫数のチェック
        if ($product->stock < $request->quantity) {
            return response()->json(['error' => '在庫が不足しています'], 400);
        }

        // トランザクション開始
        \DB::beginTransaction();

        try {
            // 在庫数の減算
            $product->stock -= $request->quantity;
            $product->save();

            // 売上の記録
            Sale::create([
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
                'price' => $product->price,
                'total' => $product->price * $request->quantity
            ]);

            // トランザクションのコミット
            \DB::commit();

            return response()->json(['message' => '購入が成功しました'], 200);
        } catch (\Exception $e) {
            // トランザクションのロールバック
            \DB::rollBack();

            return response()->json(['error' => '購入処理中にエラーが発生しました'], 500);
        }
    }
}
