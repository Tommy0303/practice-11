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
        // トランザクションを開始
        DB::beginTransaction();

        try {
            // フォームから送られてきたデータを取得
            $data = $request->all();

            // productsテーブルからidが一致する商品を取得
            $product = Product::find($data['product_id']);
            
            // 商品が存在しない場合はエラーメッセージを返して処理を中断
            if (!$product) {
                return redirect()->back()->with('error', '該当する商品が見つかりません');
            }

            // 在庫があるかどうかを確認
            if ($product->stock >= 1) {
                // 在庫を減らす
                $product->stock -= 1;
                $product->save();

                // salesテーブルにデータを保存
                $sale = new Sales();
                $sale->product_id = $product->id;
                // その他の必要なフィールドを追加

                $sale->save();

                // 成功した場合はコミット
                DB::commit();

                // 成功した場合はリダイレクトなどの処理を行う
            } else {
                // 在庫がない場合はエラーメッセージを表示してリダイレクトなど
                return redirect()->back()->with('error', '在庫が不足しています');
            }
        } catch (\Exception $e) {
            // エラーが発生した場合はロールバック
            DB::rollback();
            
            // エラー処理を行う
            // 例えば、エラーメッセージをフラッシュしてフォームにリダイレクトするなど
        }
    }
}