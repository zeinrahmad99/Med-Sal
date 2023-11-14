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


class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    /**show provider order with products which related to provider hisself */
    public function show($id){
        if(Gate::allows('isProvider'))
       { $order=Order::findOrfail($id);
        $products=$order->products;
        foreach($products as $p){
            $provider=Provider::find($p->provider_id);
           if($provider->user_id == Auth::id())
            $k[]=$p;
        }
        return response()->json([
            'status' => 1,
            'order' => $k,
        ]);}
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateOrderRequest $request)
    {
        return DB::transaction(function () use ($request){
            $data = array_merge($request->all());
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
        $order=Order::findOrfail($id);
        $this->authorize('update',$order);
        $data=$request->all();
        $order->update($data);
        return response()->json([
            'status' => 1,
            'message'=>'Update Order Successfully',
        ]);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function delete($id)
    {
       $order=Order::findOrfail($id);
       $this->authorize('update',$order);
       $order->delete();
        return response()->json([
            'status' => 1,
            'message'=>'Delete Order Successfully',

        ]);
    }
    public function approveOrder($id){
        $order=Order::find($id);

        foreach($order->products as $product)
         {
            $this->authorize('approveOrder',Order::class);
             if($product->provider->user->id == auth()->id()){
             $product->pivot->update([
                'status'=>'accepted',
             ]);}

         }
         return response()->json([
             'status'=>1,
             'massage'=>'Order Accepted Successfully',
         ]);
    }
    public function rejectOrder($id){
        $order=Order::find($id);

        foreach($order->products as $product)
         {
            $this->authorize('rejectOrder',Order::class);
             if($product->provider->user->id == auth()->id()){
             $product->pivot->update([
                'status'=>'canceled',
             ]);}

         }
         return response()->json([
             'status'=>1,
             'massage'=>'Order Canceled Successfully',
         ]);
    }
}
