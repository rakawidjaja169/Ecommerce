<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Resources\ProductResource;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = ProductResource::collection(
            Product::query()
                ->withSortBy($request->sortBy ?? '')
                ->get()
        );

        return inertia('Dashboard/Products/Index', [
            'products' => $products
        ]);
    }

    public function show(Product $product)
    {
        return inertia('Product/Show', [
            'product' => new ProductResource($product->load('category'))
        ]);
    }

    public function create()
    {
        return inertia('Dashboard/Products/Create');
    }
}
