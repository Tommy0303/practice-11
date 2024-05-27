<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Company;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Request $request)
{
    // プロダクトのクエリを作成
    $query = Product::query();

    // キーワード検索
    if ($request->filled('keyword')) {
        $query->where('product_name', 'like', '%' . $request->keyword . '%');
    }

    // メーカー検索
    if ($request->filled('manufacturer')) {
        $query->where('company_id', $request->manufacturer);
    }

    // 価格検索
    if ($request->filled('price_min')) {
        $query->where('price', '>=', $request->price_min);
    }
    if ($request->filled('price_max')) {
        $query->where('price', '<=', $request->price_max);
    }

    // 在庫数検索
    if ($request->filled('stock_min')) {
        $query->where('stock', '>=', $request->stock_min);
    }
    if ($request->filled('stock_max')) {
        $query->where('stock', '<=', $request->stock_max);
    }

    // ページネーションを適用してプロダクトを取得
    $products = $query->sortable()->paginate(10);

    // Ajaxリクエストの場合はJSONを返す
    if ($request->ajax()) {
        return response()->json([
            'products' => $products->items(),
            'links' => (string) $products->links()
        ]);
    }

    // 会社のリストを取得
    $companies = Company::all();

    // ビューにデータを渡して表示
    return view('products.index', compact('products', 'companies'));
}

    public function edit(Product $product)
    {
        $companies = Company::all(); 
        return view('products.edit', compact('product', 'companies'));
    }
    public function create()
    {
        $companies = Company::all(); 
        return view('products.create', compact('companies'));
    }
    public function store(Request $request)
{
    $request->validate([
        'product_name' => 'required|string|max:255',
        'price' => 'required|numeric',
        'stock' => 'required|numeric',
        'company_id' => 'required|string|max:255',
        'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
    ]);
    
    if ($request->hasFile('image')) {
        $image = $request->file('image');
        $fileName = $image->getClientOriginalName(); 
        $imagePath = $image->storeAs('public/images', $fileName); 
        $imageUrl = 'images/' . $fileName;
        $product = new Product();
        $product->product_name = $request->input('product_name');
        $product->price = $request->input('price');
        $product->stock = $request->input('stock');
        $product->company_id = $request->input('company_id');
        $product->img_path = $imageUrl; 

        $product->save();

        try {
        } catch (\Exception $e) {
            return back()->withError($e->getMessage());
        }

        return redirect()->route('products.index')->with('success', 'Product created successfully!');
    } else {
        return redirect()->back()->with('error', 'Image not provided');
    }
}
    public function update(Request $request, Product $product)
{
    $request->validate([
        'product_name' => 'required|string|max:255',
        'price' => 'required|numeric',
        'stock' => 'required|numeric',
        'company_id' => 'required|exists:companies,id',
        'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
    ]);

    $companyId = $request->input('company_id');

    if ($request->hasFile('image')) {
        $image = $request->file('image');
        $imagePath = $image->storeAs('public/images', $image->getClientOriginalName());
        $product->img_path = str_replace('public/', '', $imagePath);
    }

    $product->product_name = $request->input('product_name');
    $product->price = $request->input('price');
    $product->stock = $request->input('stock');
    $product->company_id = $companyId;
    $product->comment = $request->input('comment');

    $product->save();

    try {
    } catch (\Exception $e) {
        return back()->withError($e->getMessage());
    }

    return redirect()->route('products.index')->with('success', 'Product updated successfully!');
}
    public function destroy($id)
    {
        $product = Product::find($id);

        if ($product) {
            $product->delete();
            return redirect()->route('products.index')->with('success', 'Product deleted successfully');
        } else {
            return redirect()->route('products.index')->with('error', 'Product not found');
        }

        try {
        } catch (\Exception $e) {
            return back()->withError($e->getMessage());
        }
    }
    public function detail(Product $product)
    {
        return view('products.detail', compact('product'));
    }

}
