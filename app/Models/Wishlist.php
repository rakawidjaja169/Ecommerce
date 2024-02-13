<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Wishlist extends Pivot
{
    protected $table = 'wishlists';

    /**
     * @param Product $product
     * @return void
     */
    public static function toggle(Product $product): void
    {
        $productInWishlist = auth()->user()->wishlist()
            ->wherePivot('product_id', $product->id)
            ->first();

        if (!$productInWishlist) {
            auth()->user()->wishlist()->attach($product->id);
        } else {
            auth()->user()->wishlist()->detach($product->id);
        }
    }

    public static function getContent(): Collection
    {
        return auth()->user()->wishlist;
    }

    /**
     * @param Product $product
     * @return void
     */
    public static function moveToCart(Product $product): void
    {
        auth()->user()->cart()->attach($product->id, ['quantity' => 1]);
        auth()->user()->wishlist()->detach($product->id);
    }

    public static function empty(): void
    {
        auth()->user()->wishlist()->detach();
    }
}
