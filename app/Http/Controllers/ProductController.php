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

    // Product suggestions for the search box (returns JSON)
    public function suggestions(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        // Too short to search
        if (mb_strlen($q) < 2) {
            return response()->json([]);
        }

        $products = Product::with('category')
            ->where('name', 'like', '%' . $q . '%')
            ->orderByDesc('is_featured')
            ->orderBy('name')
            ->take(6)
            ->get();

        return response()->json($products->map(fn ($p) => [
            'name'     => $p->name,
            'category' => $p->category?->name ?? 'Others',
            'price'    => '₱' . number_format((float) $p->price),
            'url'      => route('products.show', $p),
            'image'    => $p->image ? asset('storage/' . $p->image) : null,
            'icon'     => $p->category?->icon ?? 'bi-box-seam',
            'stock'    => $p->stockStatus(),
        ]));
    }
}