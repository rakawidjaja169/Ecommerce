<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class CheckoutController extends Controller
{
    public function store()
    {
        $products = Cart::getContent();

        $user = Auth::user();
        $total = Cart::getCartTotal($products);

        $order = null;

        DB::transaction(function () use ($total, $user, $products, &$order) {
            /** Create the order record */
            $order = new Order();
            $order->user_id = $user->id;
            $order->status = OrderStatus::Paid;
            $order->total = $total;
            $order->save();

            /** Create the payment record */
            $payment = new Payment();
            $payment->user_id = $user->id;
            $payment->order_id = $order->id;
            $payment->status = paymentStatus::Paid;
            $payment->total_amount = $total;
            $payment->type = 'card';
            $payment->save();

            /** Create the order_product records */
            foreach ($products as $product) {
                $order->products()->attach($product->id, [
                    'quantity' => $product->pivot->quantity,
                    'unit_price' => $product->price
                ]);
            }

            /** Create the order_details records */
            $order->detail()->create([
                'customer_name' => $user['name'],
                'customer_email' => $user['email'],
                'customer_phone' => $user['phone'],
                'country' => $user['country'],
                'city' => $user['city'],
                'postalcode' => $user['postalcode'],
                'province' => $user['province'],
                'address1' => $user['address1'],
                'address2' => $user['address2'],
            ]);

            /** Empty the cart */
            $user->cart()->detach();
        });

        return redirect()->route('checkout.success')->with('order_id', $order->id);
    }

    public function success(Request $request)
    {
        try {
            $orderId = $request->session()->get('order_id');
            $customer =Auth::user();

            if (!$orderId) {
                return inertia('Checkout/Failure');
            }

            $order = Order::query()
                ->where('id', $orderId)
                ->first();

            if (!$order) {
                return inertia('Error', ['code' => 404, 'message' => 'Not found']);
            }

            return inertia('Checkout/Success', [
                'customer' => $customer,
                'order' => $order->load('products', 'detail')
            ]);
        } catch (\Exception $e) {
            return inertia('Error', ['code' => 404, 'message' => 'Not found']);
        }
    }

    public function failure(Request $request)
    {
        return inertia('Checkout/Failure');
    }
}
