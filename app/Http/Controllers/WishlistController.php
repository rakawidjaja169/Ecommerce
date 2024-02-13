<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;

class WishlistController extends Controller
{
    public function index()
    {
        return inertia('Wishlist', [
            'products' => Wishlist::getContent()
        ]);
    }

    public function toggle(Product $product)
    {
        Wishlist::toggle($product);
    }

    public function moveToCart(Product $product)
    {
        Wishlist::moveToCart($product);
    }

    public function destroy()
    {
        Wishlist::empty();
    }
}
