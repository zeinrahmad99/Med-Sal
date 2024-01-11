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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;

class ProductController extends Controller
{
    // get all products data
    public function index()
    {
        $products = Product::all();
        try {
            foreach ($products as $p) {
                if (auth('sanctum')->check()) {
                    if (($p->status != 'active' && auth('sanctum')->user()->can('admin', $p)) || $p->status == 'active') {
                        $product[] = $p;
                    }
                } else {
                    if (app()->getLocale() == 'ar') {
                        $product = Product::where('status', 'active')->
                            select('id', 'provider_id', 'category_id', 'name_' . app()->getLocale(), 'description_' . app()->getLocale(), 'price', 'discount', 'quantity', 'status', 'images', 'created_at', 'updated_at')->get();
                    } else {
                        $product = Product::where('status', 'active')->select('id', 'provider_id', 'category_id', 'name', 'description', 'price', 'discount', 'quantity', 'status', 'images', 'created_at', 'updated_at')->get();
                    }
                }
            }
            return response()->json([
                'status' => 1,
                'products' => $product,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0
            ]);
        }
    }

    // get a product data
    public function show($id)
    {
        $product = Product::find($id);
        try {
            if (auth('sanctum')->check()) {
                if (($product->status != 'active' && auth('sanctum')->user()->can('admin', $product)) || $product->status == 'active') {
                    $products = $product;
                }
            } else {
                if (app()->getLocale() == 'ar') {
                    $products = Product::where('id', $id)->where('status', 'active')->select('provider_id', 'category_id', 'name_' . app()->getLocale(), 'description_' . app()->getLocale(), 'price', 'discount', 'quantity', 'status', 'images', 'created_at', 'updated_at')->first();
                } else {
                    $products = Product::where('id', $id)->where('status', 'active')->select('provider_id', 'category_id', 'name', 'description', 'price', 'discount', 'quantity', 'status', 'images', 'created_at', 'updated_at')->first();
                }
            }
            return response()->json([
                'status' => 1,
                'product' => $products
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0
            ]);
        }
    }

    // Create a new product
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
                        // array_push($images,$filename);
                    }
                }

                // Add the images array to the data array
                $data['images'] = implode(',', $images);

                // Process the images
                // $processedImages = Images::processImages($data['images']);

                // $data['images'] = $processedImages;


                $product = Product::create($data);

                $admin = $product->category->admin->user;

                $admin->notify(new activeNotification($product, 'new request for product'));

                return response()->json([
                    'status' => 1,
                    // 'product' => $product,
                    // 'processedImages' => $processedImages
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
                    $existingImages = explode(',', $product->images);

                    foreach ($existingImages as $image) {
                        Images::deleteImage($image, 'public/images');
                    }

                    $data['images'] = [];

                    foreach ($request->file('images') as $image) {
                        $imageName = Images::giveImageRandomName($image);
                        Images::storeImage($image, $imageName, 'public/images');
                        $data['images'][] = $imageName;
                    }

                    // Convert the images array to a comma-separated string
                    $data['images'] = implode(',', $data['images']);
                }

                $product->update($data);

                return response()->json([
                    'status' => 1,
                    // 'product' => $product,
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
