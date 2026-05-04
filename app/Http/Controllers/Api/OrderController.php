<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            $totalPrice = 0;
            $orderItems = [];

            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);

                if (!$product) {
                    throw ValidationException::withMessages([
                        'product_id' => ['Product not found.'],
                    ]);
                }

                if ($item['quantity'] > $product->stock) {
                    throw ValidationException::withMessages([
                        'quantity' => ["Insufficient stock for {$product->name}. Available: {$product->stock}"],
                    ]);
                }

                $itemTotal = $product->price * $item['quantity'];
                $totalPrice += $itemTotal;

                $orderItems[] = [
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                    'product' => $product,
                ];
            }

            $order = Order::create([
                'user_id' => $request->user()->id,
                'total_price' => $totalPrice,
            ]);

            foreach ($orderItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);

                $item['product']->decrement('stock', $item['quantity']);
            }

            DB::commit();

            return response()->json([
                'message' => 'Order created successfully',
                'order' => $order->load('orderItems.product'),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Order failed',
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    public function index(Request $request)
    {
        $orders = Order::with('orderItems.product')
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'orders' => $orders,
        ]);
    }
}
