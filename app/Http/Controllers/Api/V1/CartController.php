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
        $carts = Cart::all();

        return response()->json([
            'status' => $carts ? 1 : 0,
            'carts' => $carts,
        ]);
    }

    public function store(StoreCartRequest $request)
    {
        $data = $request->all();

        $cart = Cart::create($data);

        return response()->json([
            'status' => $cart ? 1 : 0,
            'cart' => $cart
        ]);
    }

    public function delete(int $id)
    {
        $cart = Cart::firstwhere('id', $id);

        if (!$cart) {
            return response()->json([
                'status' => 0,
                'message' => 'عذراً يوجد خطأ ما'
            ]);
        }

        $deleted = $cart->delete();

        return response()->json([
            'status' => $deleted ? 1 : 0,
        ]);
    }

}
