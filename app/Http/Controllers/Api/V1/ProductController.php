<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Models\Api\V1\Product;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\CreateProductRequest;
use App\Http\Requests\Api\V1\UpdateProductRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Notifications\Api\V1\activeNotification;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();

        return response()->json([
            'status' => $products ? 1 : 0,
            'products' => $products,
        ]);
    }

    public function show($id)
    {
        $product = Product::firstwhere('id', $id);

        return response()->json([
            'status' => $product ? 1 : 0,
            'product' => $product,
        ]);
    }

    public function store(CreateProductRequest $request)
    {
      try{
            $this->authorize('create',Product::class);

            $data = array_merge($request->all(), ['status' => 'pending']);

            $product = Product::create($data);

            return response()->json([
                'status' => 1,
                'product' => $product,
            ]);

      }catch(\Exception $e){
        return response()->json([
            'status' => 0,
        ]);

      }
    }

    public function update(UpdateProductRequest $request, $id)
    {
        $product = Product::find($id);
        try{
            $this->authorize('update',$product);
            $data = $request->except('status');

            $product->update($data);

            return response()->json([
                'status' => 1,
                'product' => $product,
            ]);
        }catch(\Exception $e){
            return response()->json([
                'status' => 0,
            ]);

          }

    }


    public function delete(int $id)
    {
        $product = Product::where('id', $id)->first();
         try{
                $this->authorize('Forcedelete',$product);
                if (!$product) {
                    return response()->json([
                        'status' => 0,
                        'message' => 'عذراً يوجد خطأ ما'
                    ]);
                }

                $product = $product->delete();

                if (!$product) {
                    return response()->json([
                        'status' => 0,
                        'message' => 'عذراً يوجد خطأ ما'
                    ]);
                }

                return response()->json([
                    'status' => 1,
                ]);
         }catch(\Exception $e){
            return response()->json([
                'status' => 0,
            ]);

          }
    }
    /**remove product -> make status pending */
    public function remove($id){
        $product=Product::findOrfail($id);
         try{
                $this->authorize('remove',$product);
                return DB::transaction(function () use ($product)
                {
                $product->update(['status'=>'unaccept']);
                $user=$product->provider->user;
                $user->notify(new activeNotification($product,'The admin of category has made your product un accept'));

                return response()->json([
                    'status'=>1,
                    'message'=>'remove product successfully'
                ]);
        });
         }catch(\Exception $e){
            return response()->json([
                'status' => 0,
            ]);

          }
    }

    /* restore product make status active */

     public function accepted($id){
        $product=Product::findOrfail($id);
        try
        { $this->authorize('accepted',$product);
            return DB::transaction(function () use ($product)
            {
                $product->update(['status'=>'active']);
                $user=$product->provider->user;
                $user->notify(new activeNotification($product,'The admin of category has made your product activated'));

                return response()->json([
                    'status'=>1
                ]);
            });
        }catch(\Exception $e){
            return response()->json([
                'status' => 0,
            ]);

        }
    }

}