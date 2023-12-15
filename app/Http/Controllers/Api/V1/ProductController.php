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
use App\Traits\Api\V1\Images;

class ProductController extends Controller
{
    // Get all products.
    public function index()
    {
        if (app()->getLocale() == 'ar') {
            $products = Product::select('provider_id', 'category_id', 'name_' . app()->getLocale(), 'description_' . app()->getLocale(), 'price', 'discount', 'quantity', 'status')->get();
        } else {
            $products = Product::select('provider_id', 'category_id', 'name', 'description', 'price', 'discount', 'quantity', 'status')->get();
        }

        return response()->json([
            'status' => $products ? 1 : 0,
            'products' => $products,
        ]);
    }

    // Get a specific product by ID.
    public function show($id)
    {

        if (app()->getLocale() == 'ar') {
            $product = Product::firstwhere('id', $id)->select('provider_id', 'category_id', 'name_' . app()->getLocale(), 'description_' . app()->getLocale(), 'price', 'discount', 'quantity', 'status')->first();
        } else {
            $product = Product::firstwhere('id', $id)->select('provider_id', 'category_id', 'name', 'description', 'price', 'discount', 'quantity', 'status')->first();
        }

        return response()->json([
            'status' => $product ? 1 : 0,
            'product' => $product,
        ]);
    }

    // Create a new product.
    public function store(CreateProductRequest $request)
    {
        return DB::transaction(function () use ($request) {
            try {
                $this->authorize('create', Product::class);

                $data = array_merge($request->all(), ['status' => 'pending']);

                // Create an empty array to store the images
                $images = [];

                // Check if the request contains any images
                if ($request->hasFile('images')) {
                    foreach ($request->file('images') as $image) {
                        // Generate a unique filename for each image
                        $filename = Images::giveImageRandomName($image);

                        // Move the uploaded image to a desired location
                        Images::storeImage($image, $filename, 'public/images');
                        // $image->storeAs('public/images', $filename);

                        // Add the image filename to the array
                        $images[] = $filename;
                    }
                }

                // Add the images array to the data array
                $data['images'] = json_encode($images);

                $product = Product::create($data);

                $admin = $product->category->admin->user;

                $admin->notify(new activeNotification($product, 'new request for product'));

                return response()->json([
                    'status' => 1,
                    'product' => $product,
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => 0,
                ]);
            }
        });

    }

    // Update a product by ID.
    public function update(UpdateProductRequest $request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            try {
                $product = Product::find($id);
                $this->authorize('update', $product);
                $data = $request->except('status');

                if ($request->has('images')) {
                    $decodedImages = json_decode($product->images);

                    if (is_array($decodedImages)) {
                        foreach ($decodedImages as $image) {
                            Images::deleteImage($image, 'public/images');
                        }
                    }

                    $data['images'] = [];

                    foreach ($request->file('images') as $image) {
                        $imageName = Images::giveImageRandomName($image);
                        Images::storeImage($image, $imageName, 'public/images');
                        array_push($data['images'], $imageName);
                    }
                }

                $product->update($data);

                return response()->json([
                    'status' => 1,
                    'product' => $product,
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => 0,
                ]);
            }
        });
    }

    // Delete a product by ID.
    public function delete(int $id)
    {

        try {
            $product = Product::where('id', $id)->first();
            $this->authorize('Forcedelete', $product);
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
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
            ]);

        }
    }

    // remove product -> make status pending
    public function remove($id)
    {

        try {
            $product = Product::findOrfail($id);
            $this->authorize('remove', $product);
            return DB::transaction(function () use ($product) {
                $product->update(['status' => 'unaccept']);
                $user = $product->provider->user;
                $user->notify(new activeNotification($product, 'The admin of category has made your product un accept'));

                return response()->json([
                    'status' => 1,
                    'message' => 'remove product successfully'
                ]);
            });
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
            ]);

        }
    }

    // restore product make status active
    public function accepted($id)
    {

        try {
            $product = Product::findOrfail($id);
            $this->authorize('accepted', $product);
            return DB::transaction(function () use ($product) {
                $product->update(['status' => 'active']);
                $user = $product->provider->user;
                $user->notify(new activeNotification($product, 'The admin of category has made your product activated'));

                return response()->json([
                    'status' => 1
                ]);
            });
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
            ]);

        }
    }

}
