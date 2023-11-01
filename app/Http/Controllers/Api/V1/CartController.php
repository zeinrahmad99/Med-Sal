<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Api\V1\Cart;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreCartRequest;

class CartController extends Controller
{
    public function index()
    {
        // Retrieve all carts
        $carts = Cart::all();

        return response()->json([
            'status' => 1,
            'carts' => $carts,
        ]);
    }

    public function store(StoreCartRequest $request)
    {
        $data = $request->all();

        $cart = Cart::create($data);

        return response()->json([
            'status' => 1,
            'cart' => $cart
        ]);
    }

    public function delete(int $id)
    {
        $cart = Cart::where('id', $id)->first();

        if (!$cart) {
            return response()->json([
                'status' => 0,
                'message' => 'عذراً يوجد خطأ ما'
            ]);
        }

        $cart = $cart->delete();

        if (!$cart) {
            return response()->json([
                'status' => 0,
                'message' => 'عذراً يوجد خطأ ما'
            ]);
        }

        return response()->json([
            'status' => 1,
        ]);
    }

}
