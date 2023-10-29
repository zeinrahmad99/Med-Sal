<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Models\Api\V1\Product;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\CreateProductRequest;
use App\Http\Requests\Api\V1\UpdateProductRequest;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();

        return response()->json([
            'status' => 1,
            'products' => $products,
        ]);
    }

    public function store(CreateProductRequest $request)
    {
        $data = array_merge($request->all(), ['status' => 'pending']);

        $product = Product::create($data);

        return response()->json([
            'status' => 1,
            'product' => $product,
        ]);
    }

    public function update(UpdateProductRequest $request, $id)
    {
        $product = Product::find($id);

        $data = $request->all();

        $product->update($data);

        return response()->json([
            'status' => 1,
            'product' => $product,
        ]);
    }


    public function delete(Product $product)
    {
        $product->delete();

        return response()->json([
            'status' => 1,
        ]);
    }
}
