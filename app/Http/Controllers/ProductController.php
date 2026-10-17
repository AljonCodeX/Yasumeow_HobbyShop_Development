<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::orderBy('name')->get();
        $activeCategory = $request->integer('category') ?: null;

        $products = Product::with('category')
            ->when($activeCategory, fn ($q) => $q->where('category_id', $activeCategory))
            ->when($request->filled('q'), fn ($q) => $q->where('name', 'like', '%' . $request->q . '%'))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('shop', compact('products', 'categories', 'activeCategory'));
    }

    public function show(Product $product)
    {
        $product->load('category');

        $related = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get();

        return view('product-details', compact('product', 'related'));
    }
}