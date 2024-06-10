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
    // フォームから送られてきた検索条件を取得
    $params = $request->except('_token');
    \Log::info('Received params:', $params);

    // キーワードがnullの場合に空文字列に変換
    if (isset($params['keyword']) && is_null($params['keyword'])) {
        $params['keyword'] = '';
    }

    // セッションに検索条件を保存
    session(['searchParams' => $params]);
    \Log::info('Search parameters saved in session:', $params);

    // プロダクトのクエリを作成
    $query = Product::query();

     // セッションから検索条件を取得して再適用
     $searchParams = session('searchParams', []);
     \Log::info('Search parameters retrieved from session:', $searchParams);

    // 検索条件を適用
    if (isset($searchParams['keyword']) && !empty($searchParams['keyword'])) {
        $query->where('product_name', 'like', '%' . $searchParams['keyword'] . '%');
    }
    if (isset($searchParams['manufacturer']) && !empty($searchParams['manufacturer'])) {
        $query->where('company_id', $searchParams['manufacturer']);
    }
    if (isset($searchParams['price_min']) && !empty($searchParams['price_min'])) {
        $query->where('price', '>=', $searchParams['price_min']);
    }
    if (isset($searchParams['price_max']) && !empty($searchParams['price_max'])) {
        $query->where('price', '<=', $searchParams['price_max']);
    }
    if (isset($searchParams['stock_min']) && !empty($searchParams['stock_min'])) {
        $query->where('stock', '>=', $searchParams['stock_min']);
    }
    if (isset($searchParams['stock_max']) && !empty($searchParams['stock_max'])) {
        $query->where('stock', '<=', $searchParams['stock_max']);
    }

    // ソート条件を取得
    $sortField = $request->input('sort', 'id');
    \Log::info('Sort field before applying sort: ' . $sortField); // 追加
    $sortDirection = $request->input('direction', 'asc');
    \Log::info('Sort direction before applying sort: ' . $sortDirection); // 追加

    if ($sortField === 'company.company_name') {
        $query->join('companies as c', 'products.company_id', '=', 'c.id')
              ->orderBy('companies.company_name', $sortDirection);
    } else {
        $query->orderBy($sortField, $sortDirection);
    }

    \Log::info('Final Query:', ['query' => $query->toSql(), 'bindings' => $query->getBindings()]);


    \Log::info('Query after applying sort: ' . $query->toSql()); // 追加

    if ($request->ajax()) {
        if ($sortField && $sortDirection) {
            $query->orderBy($sortField, $sortDirection);
        }
        // ページネーションを適用してプロダクトを取得
        $products = $query->with('company')->sortable()->paginate(10)->appends($searchParams);
        $products->load('company');

        $response = [
            'products' => $products->items(),
            'links' => (string) $products->links(),
        ];
        \Log::info('AJAX response:', $response);
        return response()->json($response);
    }


    // ページネーションを適用してプロダクトを取得
    $products = $query->with('company')->sortable()->paginate(10)->appends($searchParams);
    $products->load('company');
    // 会社のリストを取得
    $companies = Company::all();

    // ビューにデータを渡して表示
    return view('products.index', compact('products', 'companies', 'searchParams', 'sortField', 'sortDirection'));
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
