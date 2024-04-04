<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Company;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();
        $companies = Company::all(); 

        if ($request->has('keyword')) {
            $query->where('product_name', 'like', '%' . $request->keyword . '%');
        }
    
        if ($request->has('manufacturer')) {
            $query->where('company_id', $request->manufacturer);
        }
    
        $products = $query->get();

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
            'company_name' => 'required|string|max:255',
        ]);
        
        $companyName = $request->input('company_name');

        $existingCompany = Company::where('company_name', $companyName)->first();
    
        if ($existingCompany) {
            $companyId = $existingCompany->id;
        } else {
            $newCompany = Company::create(['company_name' => $companyName]);
            $companyId = $newCompany->id;
        }
        
        $product = new Product();
        $product->product_name = $request->input('product_name');
        $product->price = $request->input('price');
        $product->stock = $request->input('stock');
        $product->company_id = $companyId;

        $product->save();

        return redirect()->route('products.index')->with('success', 'Product created successfully!');
    }
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'product_name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'stock' => 'required|numeric',
            'company_name' => 'required|string|max:255', 
        ]);
    
        $companyName = $request->input('company_name');
    
        $existingCompany = Company::where('company_name', $companyName)->first();
    
        if ($existingCompany) {
            $companyId = $existingCompany->id;
        } else {
            $newCompany = Company::create(['company_name' => $companyName]);
            $companyId = $newCompany->id;
        }
    
        $product->product_name = $request->input('product_name');
        $product->price = $request->input('price');
        $product->stock = $request->input('stock');
        $product->company_id = $companyId;
        $product->save();

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
    }
    public function detail(Product $product)
    {
        return view('products.detail', compact('product'));
    }

}

