<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Api\V1\Order;
use App\Models\Api\V1\Product;
use App\Models\Api\V1\Provider;
use App\Http\Requests\Api\V1\CreateOrderRequest;
use App\Http\Requests\Api\V1\UpdateOrderRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use App\Notifications\Api\V1\OrderNotification;

class OrderController extends Controller
{
    // Display a listing of the resource.
    // show provider order with products which related to provider hisself 
    public function show($id)
    {
        $order = Order::find($id);
        if (auth('sanctum')->user()->can('isProvider')) {
            $order = Order::where('id', $id)->select('id', 'patient_id', 'latitude', 'longitude', 'created_at', 'updated_at')
            ->with('products'
                , function ($query) use ($id) {
                    $query->where('order_product.order_id', $id);
                    $query->select('order_product.product_id', 'order_product.status', 'order_product.price', 'order_product.quantity','order_product.created_at as date_of_request','order_product.updated_at');
                })->get();
            return response()->json([
                'status' => 1,
                'order' => $order,
            ]);
        } else if (auth('sanctum')->user()->can('view', $order)) {
            $order = Order::where('id', $id)->select('id', 'patient_id', 'latitude', 'longitude', 'created_at', 'updated_at')
        ->with('products'
                , function ($query) use ($id) {
                    $query->select('order_product.product_id', 'order_product.status', 'order_product.price', 'order_product.quantity','order_product.created_at as date_of_request','order_product.updated_at');
                })->get();
            return response()->json([
                'status' => 1,
                'order' => $order,
            ]);
        } else {
            return response()->json([
                'status' => 0
            ]);
        }

    }

    // Store a newly created resource in storage.
    public function store(CreateOrderRequest $request)
    {
        return DB::transaction(function () use ($request) {
            $data = array_merge($request->all(), ['patient_id' => Auth::id(), 'cost' => 0]);
            $order_products = $request->input('products');
            $cost = 0;
            $order = Order::create($data);
            foreach ($order_products as $order_product) {
                $product = Product::find($order_product['product_id']);
                $price = $product->price * $order_product['quantity'];
                if ($product->quantity >= $order_product['quantity']) {
                    $order->products()->attach($order->id,
                        ['product_id' => $order_product['product_id'],
                            'quantity' => $order_product['quantity'],
                            'price' => $price,
                        ]);

                    $cost += $price;
                    $order->update(['cost' => $cost]);
                    $product->quantity = $product->quantity - $order_product['quantity'];
                    $product->save();
                    $provider = $product->provider->user;
                    $provider->notify(new OrderNotification($product, 'new order store'));
                } else {
                    DB::rollback();
                    return response()->json([
                        'status' => 0,
                    ]);
                }
            }
            if (!$order || !$order_products) {
                DB::rollback();
                return response()->json([
                    'status' => 0
                ]);
            }
            return response()->json([
                'status' => 1,
                'message' => 'Add Order Successfully',
            ]);
        });
    }

    // Update the specified resource in storage.
    public function update(UpdateOrderRequest $request, $id)
    {

        try {
            $order = Order::findOrfail($id);
            $this->authorize('update', $order);
            $data = $request->all();
            $order->update($data);
            return response()->json([
                'status' => 1,
                'message' => 'Update Order Successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0
            ]);
        }
    }

    // Remove the specified resource from storage.
    public function delete($id)
    {

        try {
            $order = Order::findOrfail($id);
            $this->authorize('update', $order);
            $order->delete();
            return response()->json([
                'status' => 1,
                'message' => 'Delete Order Successfully',

            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
            ]);
        }
    }

    // approve the Order
    public function approveOrder($id)
    {
        try {
            $order = Order::find($id);
            return DB::transaction(function () use ($order) {

                foreach ($order->products as $product) {
                    $this->authorize('approveOrder', Order::class);
                    if ($product->provider->user->id == auth()->id()) {
                        $product->pivot->update([
                            'status' => 'accepted',
                        ]);
                        $user = $order->user;
                        $user->notify(new OrderNotification($product, 'your order accepted'));
                    }

                }
                return response()->json([
                    'status' => 1,
                    'massage' => 'Order Accepted Successfully',
                ]);
            });
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
            ]);
        }
    }

    // reject the Order
    public function rejectOrder($id)
    {

        try {
            $order = Order::find($id);
            return DB::transaction(function () use ($order) {
                foreach ($order->products as $product) {
                    $this->authorize('rejectOrder', Order::class);
                    if ($product->provider->user->id == auth()->id()) {
                        $product->pivot->update([
                            'status' => 'canceled',
                        ]);
                        $user = $order->user;
                        $user->notify(new OrderNotification($product, 'your order canceled'));
                    }

                }
                return response()->json([
                    'status' => 1,
                    'massage' => 'Order Canceled Successfully',
                ]);
            });
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0
            ]);
        }
    }
}
