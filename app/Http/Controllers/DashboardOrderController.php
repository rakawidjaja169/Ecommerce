<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Order;
use App\Models\Product;
use App\Enums\OrderStatus;
use Illuminate\Http\Request;
use App\Http\Resources\OrderResource;

class DashboardOrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = OrderResource::collection(
            Order::query()
                ->where('user_id', auth()->id())
                ->with('products', 'detail')
                ->withSortBy($request->sortBy ?? '')
                ->get()
        );

        return inertia('Dashboard/Order', [
            'orders' => $orders
        ]);
    }

    public function update(Request $request, Order $order)
    {
        // Retrieve the products associated with the order
        $products = $order->products;

        foreach ($products as $product) {    
            if ($product->pivot->quantity > $product->available_quantity || $order->status === OrderStatus::Approved->value) {
                return back()->with('error', 'The quantity requested is greater than the available quantity or the order has already been approved.');
                // return inertia('Error', ['code' => 400, 'message' => 'The quantity requested is greater than the available quantity or the order has already been approved.']);
            }    
            // Calculate the new quantity for the product
            $newQuantity = $product->available_quantity - $product->pivot->quantity;
    
            // Update the product quantity in the products table
            Product::where('id', $product->id)->update(['available_quantity' => $newQuantity]);

            // Change order status into Approved
            $order->update(['status' => OrderStatus::Approved->value]);
        }

        return back()->with('message', 'Order updated successfully!');
    }
}
