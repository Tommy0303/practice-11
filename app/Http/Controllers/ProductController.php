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
        $query = Product::query();
        $companies = Company::all(); 

        if ($request->has('keyword')) {
            $keyword = $request->keyword;
            $query->where('product_name', 'like', '%' . $keyword . '%');
        }

            if ($request->filled('keyword') && !$request->filled('manufacturer')) {
                $products = $query->get();
            } else {
                // キーワードが入力されていない、またはメーカーが選択されている場合、通常の検索を行う
                if ($request->filled('manufacturer')) {
                    $query->where('company_id', $request->manufacturer);
                }
                $products = $query->get();
            }

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
