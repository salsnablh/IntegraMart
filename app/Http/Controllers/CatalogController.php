<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function index(Request $request): View
    {
        $categories = Product::query()
            ->where('is_active', true)
            ->whereNotNull('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        $products = Product::query()
            ->where('is_active', true)
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('name', 'like', '%'.$request->string('search')->toString().'%');
            })
            ->when($request->filled('category'), function ($query) use ($request) {
                $query->where('category', $request->string('category')->toString());
            })
            ->when($request->string('stock')->toString() === 'available', function ($query) {
                $query->where('stock', '>', 0);
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('shop.index', compact('products', 'categories'));
    }

    public function show(Product $product): View
    {
        abort_unless($product->is_active, 404);

        $relatedProducts = Product::query()
            ->where('is_active', true)
            ->whereKeyNot($product->id)
            ->where('category', $product->category)
            ->limit(4)
            ->get();

        return view('shop.show', compact('product', 'relatedProducts'));
    }
}
