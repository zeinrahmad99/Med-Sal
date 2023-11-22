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
    /**
     * Display a listing of the resource.
     */

    /**show provider order with products which related to provider hisself */
    public function show($id){
    try
       {
         Gate::authorize('isProvider');
            {
                 $order=Order::findOrfail($id);
                $products=$order->products;
                foreach($products as $product){
                    $provider=Provider::find($product->provider_id);
                if($provider->user_id == Auth::id())
                    $res[]=$product;
                }

                return response()->json([
                    'status' => 1,
                    'order' => $order,
                 ]);
            }
        }catch(\Exception $e){
            return response()->json([
                'status' => 0,
             ]);
        }
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateOrderRequest $request)
    {
        return DB::transaction(function () use ($request){
            $data = array_merge($request->all() , ['patient_id' => Auth::id()]);
            $order_products=$request->input('products');
            $order=Order::create($data);
           foreach($order_products as $order_product)
                {
                   $product=Product::find($order_product['product_id']);
                    if($product->quantity >= $order_product['quantity']){
                          $order->products()->attach($order->id,
                        ['product_id'=>$order_product['product_id'],
                        'quantity'=>$order_product['quantity'],
                         ]);
                        $product->quantity =$product->quantity - $order_product['quantity'];
                        $product->save();
                        $provider=$product->provider->user;
                        $provider->notify(new OrderNotification($product,'new order store'));
                    }
                    else{
                        DB::rollback();
                        return response()->json([
                            'status' => 0,
                    ]);
                    }
                }
                if(! $order || ! $order_products) {
                    DB::rollback();
                    return response()->json([
                        'status' =>0
                ]);
                }
            return response()->json([
                'status' =>1,
                'message' => 'Add Order Successfully'
            ]);
    });
}

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrderRequest $request,$id)
    {

        try
        {
            $order=Order::findOrfail($id);
            $this->authorize('update',$order);
            $data=$request->all();
            $order->update($data);
            return response()->json([
                'status' => 1,
                'message'=>'Update Order Successfully',
            ]);
        }catch(\Exception $e){
            return response()->json([
                'status' => 0
             ]);
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function delete($id)
    {

        try
        {       $order=Order::findOrfail($id);
                $this->authorize('update',$order);
                $order->delete();
                return response()->json([
                    'status' => 1,
                    'message'=>'Delete Order Successfully',

                ]);
        }catch(\Exception $e){
            return response()->json([
                'status' => 0,
             ]);
        }
    }

    public function approveOrder($id){
        try
            {
                $order=Order::find($id);
                return DB::transaction(function () use ($order){

                    foreach($order->products as $product)
                    {
                        $this->authorize('approveOrder',Order::class);
                        if($product->provider->user->id == auth()->id()){
                        $product->pivot->update([
                            'status'=>'accepted',
                        ]);
                        $user=$order->user;
                        $user->notify(new OrderNotification($product,'your order accepted'));
                    }

                    }
                    return response()->json([
                        'status'=>1,
                        'massage'=>'Order Accepted Successfully',
                    ]);
                });
            }catch(\Exception $e){
                return response()->json([
                    'status'=>0,
                ]);
            }
    }

    public function rejectOrder($id){

       try
        {
            $order=Order::find($id);
            return DB::transaction(function () use ($order)
            {
                foreach($order->products as $product)
                {
                $this->authorize('rejectOrder',Order::class);
                    if($product->provider->user->id == auth()->id()){
                    $product->pivot->update([
                    'status'=>'canceled',
                    ]);
                    $user=$order->user;
                    $user->notify(new OrderNotification($product,'your order canceled'));
                }

                }
                return response()->json([
                    'status'=>1,
                    'massage'=>'Order Canceled Successfully',
                ]);
            });
        }catch(\Exception $e){
            return response()->json([
                'status'=>0
            ]);
       }
    }
}