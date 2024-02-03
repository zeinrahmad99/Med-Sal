<?php

namespace App\Traits\Api\V1;

use App\Models\Api\V1\Product;
use Illuminate\Support\Facades\Storage;

trait Images
{
    // This function changes the name of uploaded image files
    public static function giveImageRandomName($image)
    {
        return uniqid() . '.' . $image->getClientOriginalExtension();
    }

    // This function deletes an image file
    public static function deleteImage($image, string $path)
    {
        $imagePath = public_path($path . $image);
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }
    // public static function deleteImage($image, string $path)
    // {
    //     Storage::delete($path . $image);
    // }

    // This function stores image files
    public static function storeImage($image, string $imageName, string $path)
    {
        $image->move(public_path($path), $imageName);
    }
    // public static function storeImage($image, string $imageName, string $path)
    // {
    //     // $image->move($path, $imageName);
    //     Storage::putFileAs($path, $image, $imageName);
    // }

    public static function processImages($images)
    {
        $processedImages = [];

        if (is_null($images)) {
            $processedImages[] = asset('images/default_images.jpg');
        } else {
            $images = explode(',', $images);
            foreach ($images as $image) {
                if (str_starts_with($image, 'https://')) {
                    $processedImages[] = $image;
                } else {
                    $processedImages[] = asset('images/' . $image);
                }
            }
        }

        return $processedImages;
        // return implode(',', $processedImages);
    }

    // do not remove, its for testing

    // // Build the query for searching nearest services.
    // public function buildQueryForNearestServices($latitude, $longitude, $distance, $sortByDistance)
    // {
    //     $query = $this->query
    //         ->select('categories.*')
    //         ->with([
    //             'services' => fn($query) => $query->active(),
    //             'providers' => fn($query) => $query->active(),
    //         ])
    //         ->join('providers', 'categories.id', '=', 'providers.service_type_id')
    //         ->join('service_locations as sl', 'providers.id', '=', 'sl.provider_id')
    //         ->selectRaw($this->calculateHaversineDistance($latitude, $longitude, 'sl.latitude', 'sl.longitude') . ' AS distance')
    //         ->where('providers.deleted_at', null)
    //         ->having('distance', '<=', $distance);

    //     if ($sortByDistance) {
    //         $query->orderByRaw('distance');
    //     }

    //     return $query;
    // }

    // Build the query for searching services by location.
    // public function buildQueryForLocationSearch($latitude, $longitude, $distance)
    // {
    //     $haversineFormula = $this->calculateHaversineDistance($latitude, $longitude, 'service_locations.latitude', 'service_locations.longitude');

    //     return $this->query
    //         ->with([
    //             'services' => fn($query) => $query->active(),
    //             'providers' => fn($query) => $query->active(),
    //             'providers.serviceLocations'
    //         ])
    //         ->whereHas('providers.serviceLocations', fn($query) => $query
    //             ->selectRaw("service_locations.*, $haversineFormula AS distance")
    //             ->having('distance', '<=', $distance)
    //             ->orderBy('distance')
    //         );
    // }

    // Build the query for searching services by location.
    // public function buildQueryForLocationSearch($latitude, $longitude, $distance)
    // {
    //     $haversineFormula = $this->calculateHaversineDistance($latitude, $longitude, 'service_locations.latitude', 'service_locations.longitude');

    //     return $this->query
    //         ->with([
    //             'services' => fn($query) => $query->active(),
    //             'providers' => fn($query) => $query->active(),
    //             'providers.serviceLocations'
    //         ])
    //         ->whereHas('providers.serviceLocations', fn($query) => $query
    //             ->selectRaw("service_locations.*, $haversineFormula AS distance")
    //             ->having('distance', '<=', $distance)
    //             ->orderBy('distance')
    //         );
    // }

    // // Build the query for searching nearest services.
    // public function buildQueryForNearestServices($latitude, $longitude, $distance, $sortByDistance)
    // {
    //     $query = $this->query
    //         ->select('categories.*')
    //         ->with([
    //             'services' => fn($query) => $query->active(),
    //             'providers' => fn($query) => $query->active(),
    //         ])
    //         ->join('providers', 'categories.id', '=', 'providers.service_type_id')
    //         ->join('service_locations as sl', 'providers.id', '=', 'sl.provider_id')
    //         ->selectRaw($this->calculateHaversineDistance($latitude, $longitude, 'sl.latitude', 'sl.longitude') . ' AS distance')
    //         ->where('providers.deleted_at', null)
    //         ->having('distance', '<=', $distance);

    //     if ($sortByDistance) {
    //         $query->orderByRaw('distance');
    //     }

    //     return $query;
    // }

    // public function searchDoctorsByServiceName($name)
    // {
    //     $query = $this->query
    //         ->whereHas('services', function ($query) use ($name) {
    //             $query
    //                 ->where('name', 'like', '%' . $name . '%');
    //         });

    //     if ($query->count() > 0) {
    //         $query->select('id', 'admin_id', 'name', 'description', 'status');
    //     } else {
    //         $query = $this->query
    //             ->whereHas('services', function ($query) use ($name) {
    //                 $query
    //                     ->where('name_ar', 'like', '%' . $name . '%');
    //             });
    //         $query->select('id', 'admin_id', 'name_ar', 'description_ar', 'status');
    //     }

    //     return $query->with([
    //         'providers' => function ($query) {
    //             $query->active();
    //         }
    //     ])->active();
    // }

    // // Search doctors by service name within a category.
    // public function searchDoctorsByServiceName($serviceName)
    // {
    //     return $this->query
    //         ->whereHas('services', fn($query) => $query
    //             ->where('name', $serviceName)
    //             ->orWhere('name_ar', $serviceName))
    //         ->active()
    //         ->with([
    //             'providers' => fn($query) => $query->active()
    //         ])
    //         ->active();
    // }

    // Search services and products within a category by name.
    //   public function searchServicesProductsByCategoryName($name)
    //   {
    //       return $this->query
    //           ->where('name', $name)
    //           ->orwhere('name_ar', $name)
    //           ->with([
    //               'services' => fn($query) => $query->active(),
    //               'products' => fn($query) => $query->active(),
    //           ]);
    //   }

    // Search services within a category by name.
    // public function searchServicesByCategoryName($name)
    // {
    // return $this->query
    //     ->where('name', $name)
    //     ->orwhere('name_ar', $name)
    //     ->with([
    //         'services' => fn($query) => $query->active()
    //     ]);
    // }



    // public function searchProductsByCategoryName($name)
    // {
    //     return $this->query
    //         ->where('name', $name)
    //         ->orwhere('name_ar', $name)
    //         ->with([
    //             'products' => fn($query) => $query->active(), 
    //         ]);
    // }
    // public function searchByCategoryName($name)
    // {
    //     return $this->query
    //         ->where('name', 'like', '%' . $name . '%')
    //         ->orwhere('name_ar', 'like', '%' . $name . '%')
    //         ->active();
    // }
    // // Generate a monthly report for products sold by a provider.
    // public function reportProduct($provider)
    // {

    //     try {
    //         $this->authorize('admin', Provider::find($provider));
    //         $product[] = DB::table('products')->where('provider_id', '=', $provider)
    //             ->join('order_product', 'products.id', '=', 'order_product.product_id')
    //             ->join('orders', 'order_product.order_id', '=', 'orders.id')
    //             ->join('providers', 'products.provider_id', '=', 'providers.id')
    //             ->select(
    //                 'products.name',
    //                 'order_product.status',
    //                 'orders.updated_at  as date',
    //                 'providers.bussiness_name as provider_name',
    //                 DB::raw('SUM(order_product.quantity * (products.price - (products.price * products.discount / 100))) as total_amount')
    //             )
    //             ->whereMonth('orders.created_at', date('m'))->where('order_product.status', '=', 'delivered')

    //             ->groupBy('products.name', 'order_product.product_id', 'order_product.status', 'orders.updated_at', 'providers.bussiness_name')
    //             ->get();
    //         return response()->json([
    //             'status' => 1,
    //             'sales' => $product,
    //         ]);

    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status' => 0,
    //         ]);
    //     }

    // }

    // Generate a monthly report for services provided by a provider.
    // public function reportService($provider)
    // {
    //     try {
    //     $this->authorize('admin', Provider::find($provider));

    //     $services = DB::table('service_locations')
    //         ->where('provider_id', '=', $provider)
    //         ->join('services', 'service_locations.id', '=', 'services.service_location_id')
    //         ->join('appointments', 'services.id', '=', 'appointments.service_id')
    //         ->join('providers', 'providers.id', '=', 'service_locations.provider_id')
    //         ->select(
    //             'services.name',
    //             'appointments.status',
    //             'appointments.updated_at as date',
    //             'providers.bussiness_name as provider_name',
    //             DB::raw('SUM(services.price - (services.price * services.discount / 100)) as total_amount')
    //         )
    //         ->whereMonth('appointments.created_at', date('m'))->where('appointments.status', '=', 'done')
    //         ->groupBy(
    //             'services.name',
    //             'appointments.status',
    //             'appointments.updated_at',
    //             'providers.bussiness_name'
    //         )
    //         ->get();
    //     return response()->json([
    //         'status' => 1,
    //         'sales' => $services
    //     ]);

    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status' => 0,
    //         ]);
    //     }
    // }
    // // Get all services for authenticated users.
    // public function indexAuth()
    // {
    //     if (app()->getLocale() == 'ar') {
    //         $services = Service::select('category_id', 'service_location_id', 'name_' . app()->getLocale(), 'description_' . app()->getLocale(), 'price', 'discount', 'status')->get();
    //         ;
    //     } else {
    //         $services = Service::select('category_id', 'service_location_id', 'name', 'description', 'price', 'discount', 'status')->get();
    //         ;

    //     }

    //     return response()->json([
    //         'status' => $services ? 1 : 0,
    //         'services' => $services,
    //     ]);
    // }

    // // Remove the specified resource from storage.
    // public function delete($id)
    // {
    //     try {
    //         Gate::authorize('isSuperAdmin');

    //         return DB::transaction(function () use ($id) {
    //             $admin = Admin::where('admin_id', $id)->first();
    //             $user = User::find($id);
    //             $user->update(['role' => 'patient']);
    //             $admin->delete();
    //             return response()->json([
    //                 'status' => 1,
    //             ]);
    //         });
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status' => 0,
    //         ]);
    //     }

    // }
    // // Get all products.
    // public function index()
    // {
    //     // TODO Permissions
    //     if (app()->getLocale() == 'ar') {
    //         $products = Product::select('provider_id', 'category_id', 'name_' . app()->getLocale(), 'description_' . app()->getLocale(), 'price', 'discount', 'quantity', 'status', 'images')->get();
    //     } else {
    //         $products = Product::select('provider_id', 'category_id', 'name', 'description', 'price', 'discount', 'quantity', 'status', 'images')->get();
    //     }

    //     return response()->json([
    //         'status' => $products ? 1 : 0,
    //         'products' => $products,
    //     ]);
    // }
    // // Get a specific product by ID.
    // public function show($id)
    // {
    //     // TODO Permissions
    //     if (app()->getLocale() == 'ar') {
    //         $product = Product::firstwhere('id', $id)->select('provider_id', 'category_id', 'name_' . app()->getLocale(), 'description_' . app()->getLocale(), 'price', 'discount', 'quantity', 'status', 'images')->first();
    //     } else {
    //         $product = Product::firstwhere('id', $id)->select('provider_id', 'category_id', 'name', 'description', 'price', 'discount', 'quantity', 'status', 'images')->first();
    //     }

    //     return response()->json([
    //         'status' => $product ? 1 : 0,
    //         'product' => $product,
    //     ]);
    // }   // // Get all products.
    // public function index()
    // {

    //     $products = $this->getProductData();

    //     return response()->json([
    //         'status' => $products ? 1 : 0,
    //         'products' => $products,
    //     ]);
    // }


    // // Get a specific product by ID.
    // public function show($id)
    // {
    //     
    //     $product = $this->getProductData($id);

    //     return response()->json([
    //         'status' => $product ? 1 : 0,
    //         'product' => $product,
    //     ]);
    // }

    // // Helper method to fetch product data 
    // private function getProductData($id = null)
    // {
    //     $locale = app()->getLocale();
    //     $query = Product::query();

    //     if ($id !== null) {
    //         $query->where('id', $id);
    //     }

    //     $selectColumns = [
    //         'provider_id',
    //         'category_id',
    //         'name',
    //         'description',
    //         'price',
    //         'discount',
    //         'quantity',
    //         'status',
    //         'images',
    //     ];

    //     if ($locale == 'ar') {
    //         foreach ($selectColumns as $key => $column) {
    //             if ($column === 'name' || $column === 'description') {
    //                 $selectColumns[$key] = $column . '_' . $locale;
    //             }
    //         }
    //     }

    //     $products = $query->select($selectColumns)->get();

    //     $productsData = [];

    //     foreach ($products as $product) {
    //         $images = [];

    //         if ($product->images) {
    //             $imagePaths = explode(',', $product->images);

    //             foreach ($imagePaths as $imagePath) {
    //                 $imageUrl = Storage::url('public/images/' . $imagePath);
    //                 $images[] = $imageUrl;
    //             }
    //         }

    //         $productData = $product->toArray();
    //         $productData['images'] = $images;

    //         $productsData[] = $productData;
    //     }

    //     return $productsData;
    // }
    // // Update a product by ID.
    // public function update(UpdateProductRequest $request, $id)
    // {
    //     return DB::transaction(function () use ($request, $id) {
    //         try {
    //             $product = Product::find($id);
    //             // $this->authorize('update', $product);
    //             $data = $request->except('status');

    //             if ($request->has('images')) {
    //                 $decodedImages = json_decode($product->images);

    //                 if (is_array($decodedImages)) {
    //                     foreach ($decodedImages as $image) {
    //                         Images::deleteImage($image, 'public/images');
    //                     }
    //                 }

    //                 $data['images'] = [];

    //                 foreach ($request->file('images') as $image) {
    //                     $imageName = Images::giveImageRandomName($image);
    //                     Images::storeImage($image, $imageName, 'public/images');
    //                     array_push($data['images'], $imageName);

    //                 }
    //             }

    //             $product->update($data);

    //             return response()->json([
    //                 'status' => 1,
    //                 'product' => $product,
    //                 // 'product' => $product->toArray(),
    //             ]);
    //         } catch (\Exception $e) {
    //             return response()->json([
    //                 'status' => 0,
    //             ]);
    //         }
    //     });
    // }
    // // Get all products.
    // public function index()
    // {
    //     if (app()->getLocale() == 'ar') {
    //         $products = Product::select('provider_id', 'category_id', 'name_' . app()->getLocale(), 'description_' . app()->getLocale(), 'price', 'discount', 'quantity', 'status')->get();
    //     } else {
    //         $products = Product::select('provider_id', 'category_id', 'name', 'description', 'price', 'discount', 'quantity', 'status')->get();
    //     }

    //     return response()->json([
    //         'status' => $products ? 1 : 0,
    //         'products' => $products,
    //     ]);
    // }

    // // Get a specific product by ID.
    // public function show($id)
    // {

    //     if (app()->getLocale() == 'ar') {
    //         $product = Product::firstwhere('id', $id)->select('provider_id', 'category_id', 'name_' . app()->getLocale(), 'description_' . app()->getLocale(), 'price', 'discount', 'quantity', 'status')->first();
    //     } else {
    //         $product = Product::firstwhere('id', $id)->select('provider_id', 'category_id', 'name', 'description', 'price', 'discount', 'quantity', 'status')->first();
    //     }

    //     return response()->json([
    //         'status' => $product ? 1 : 0,
    //         'product' => $product,
    //     ]);
    // }
}