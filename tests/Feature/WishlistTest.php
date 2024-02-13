<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class WishlistTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_add_product_to_wishlist()
    {
        $this->post('/wishlist/1')
            ->assertRedirect('/login');
    }

    public function test_user_can_add_product_to_wishlist()
    {
        Category::factory()->create();
        Product::factory(2)->create();

        $this->actingAs(User::factory()->create());

        $this->post('/wishlist/2');

        $this->assertDatabaseHas('wishlists', [
            'user_id' => 1,
            'product_id' => 2,
        ]);
    }

    public function test_if_product_is_already_present_in_the_wishlist_it_is_removed()
    {
        Category::factory()->create();
        Product::factory(2)->create();

        $this->actingAs(User::factory()->create());

        $this->post('/wishlist/2');

        $this->assertDatabaseHas('wishlists', [
            'user_id' => 1,
            'product_id' => 2,
        ]);

        $this->post('/wishlist/2');

        $this->assertDatabaseMissing('wishlists', [
            'user_id' => 1,
            'product_id' => 2,
        ]);
    }

    public function test_user_can_view_his_wishlist()
    {
        Category::factory()->create();
        Product::factory(1)->create();

        $this->actingAs(User::factory()->create());

        auth()->user()->wishlist()->attach(1);

        $this->get('wishlist')
            ->assertInertia(function (AssertableInertia $page) {
                $page
                    ->component('Wishlist')
                    ->has('products', 1);
            });
    }

    public function test_user_can_empty_his_wishlist()
    {
        Category::factory()->create();
        Product::factory(2)->create();

        $this->actingAs(User::factory()->create());

        auth()->user()->wishlist()->attach(1);
        auth()->user()->wishlist()->attach(2);

        $this->assertDatabaseCount('wishlists', 2);

        $this->delete('/wishlist');

        $this->assertDatabaseCount('wishlists', 0);
    }

    public function test_user_can_move_a_product_from_wishlist_to_cart()
    {
        Category::factory()->create();
        Product::factory()->create();

        $this->actingAs(User::factory()->create());

        auth()->user()->wishlist()->attach(1);

        $this->assertDatabaseCount('wishlists', 1);

        $this->post('/wishlist/1/move-to-cart');

        $this->assertDatabaseCount('wishlists', 0);
        $this->assertDatabaseHas('carts', [
            'user_id' => 1,
            'product_id' => 1,
            'quantity' => 1,
            'saved_for_later' => false,
        ]);
    }
}
