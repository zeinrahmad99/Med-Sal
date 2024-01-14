<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Models\Api\V1\Product;
use App\Models\Api\V1\Service;
use App\Models\Api\V1\Category;
use App\Models\Api\V1\Provider;
use App\Models\Api\V1\User;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Notifications\Api\V1\AdminNotification;
use App\Notifications\Api\V1\ProviderNotification;
use App\Http\Requests\Api\V1\UpdateProviderRequest;
use App\Models\Api\V1\ProviderProfileUpdateRequest;

class ProviderController extends Controller
{

    // get all update requests for providers profile details
    public function indexUpdateRequestsProviders()
    {
        $provider = ProviderProfileUpdateRequest::all();
        try {
            foreach ($provider as $p) {
                if (auth('sanctum')->user()->can('admin', Provider::find($p->provider_id))) {
                    $res[] = $p;
                }
            }

            return response()->json([
                'status' => 1,
                'data' => $res
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0
            ]);
        }
    }

    // get all providers where status is pending
    public function indexProviders()
    {
        $provider = Provider::where('status', 'pending')->get();
        try {
            foreach ($provider as $p) {
                if (auth('sanctum')->user()->can('admin', $p)) {
                    $res[] = $p;
                }
            }

            return response()->json([
                'status' => 1,
                'data' => $res,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0
            ]);
        }
    }

    // get all providers
    public function index()
    {
        $provider = Provider::all();
        try {
            foreach ($provider as $p) {
                if (auth('sanctum')->user()->can('admin', $p)) {
                    $res[] = $p;
                }
            }

            return response()->json([
                'status' => 1,
                'data' => $res,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0
            ]);
        }
    }

    // get provider data with user related data
    public function showProvider($id)
    {
        try {
            $provider = Provider::find($id)->load('user');

            if (auth('sanctum')->user()->can('admin', $provider)) {
                return response()->json([
                    'status' => 1,
                    'data' => $provider,
                ]);
            } else {
                return response()->json([
                    'status' => 0,
                    'message' => 'Unauthorized',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'Provider not found',
            ]);
        }
    }

    // Update the provider's profile update request.
    public function updateRequest(UpdateProviderRequest $request, $id)
    {
        $provider = Provider::find($id);
        try {
            return DB::transaction(
                function () use ($provider, $request) {
                    $this->authorize('update', $provider);
                    $requestData = $request->all();
                    $requestData['provider_id'] = $provider->id;

                    $updateRequest = ProviderProfileUpdateRequest::create($requestData);

                    $provider->update(['status' => 'pending']);
                    $admin = $provider->category->admin->user;
                    $admin->notify(new AdminNotification($provider));

                    return response()->json([
                        'status' => 1,
                        'provider' => $updateRequest,
                        'message' => 'تم إرسال طلب التحديث بنجاح',

                    ]);
                }
            );
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
            ]);
        }
    }

    // approveUpdateRequest
    public function approveUpdateRequest($id)
    {
        try {
            $updateRequest = ProviderProfileUpdateRequest::find($id);
            $provider = $updateRequest->provider;

            $this->authorize('update', $provider);

            return DB::transaction(function () use ($updateRequest, $provider) {
                // Update provider data
                $provider->update([
                    'bussiness_name' => $updateRequest->business_name,
                    'contact_number' => $updateRequest->contact_number,
                    'bank_name' => $updateRequest->bank_name,
                    'iban' => $updateRequest->iban,
                    'swift_code' => $updateRequest->swift_code,
                ]);

                // Delete the update request
                $updateRequest->delete();

                return response()->json([
                    'status' => 1,
                    'message' => 'تمت الموافقة على طلب التحديث بنجاح',
                ]);
            });
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'فشلت عملية الموافقة على طلب التحديث',
            ]);
        }

    }
    // rejectUpdateRequest
    public function rejectUpdateRequest($id)
    {
        try {
            $updateRequest = ProviderProfileUpdateRequest::find($id);

            $provider = $updateRequest->provider;
            $this->authorize('rejectProvider', $provider);

            $updateRequest->delete();

            return response()->json([
                'status' => 1,
                'message' => 'تم رفض طلب التحديث بنجاح',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'فشلت عملية رفض طلب التحديث',
            ]);
        }

    }

    // Delete a provider.
    public function delete(int $id)
    {
        if (auth('sanctum')->user()->can('admin', Provider::find($id))) {

            $provider = Provider::where('id', $id)->first();

            if (!$provider) {
                return response()->json([
                    'status' => 0,
                    'message' => 'عذراً يوجد خطأ ما'
                ]);
            }

            $provider = $provider->delete();
        }

        if (!$provider) {
            return response()->json([
                'status' => 0,
                'message' => 'عذراً يوجد خطأ ما'
            ]);
        }

        return response()->json([
            'status' => 1,
        ]);
    }

    // Display the products, services of each service provider 
    public function displayProviderDetails($id)
    {
        try {
            $provider = Provider::find($id);
            $this->authorize('view', $provider);
            $product = $provider->products;
            $p_services = $provider->serviceLocations;
            foreach ($p_services as $service) {
                $providerService[] = $service->services;
            }
            if (!$p_services->isEmpty()) {
                return response()->json([
                    'services' => $providerService,
                    'products' => $product,
                ]);
            } else {
                return response()->json([
                    'products' => $product,
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
            ]);
        }
    }
    // Approve request service provider 
    public function approveProvider($id)
    {
        try {
            $provider = Provider::find($id);
            $this->authorize('approveProvider', $provider);
            return DB::transaction(function () use ($provider) {
                $provider->update(['status' => 'active']);

                $user = $provider->user;
                $user->notify(new ProviderNotification($provider, 'The admin of category has confirmed your request to be a service provider'));
                return response()->json([
                    'status' => 1,
                ]);
            });
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
            ]);
        }
    }

    // reject  request service provider 
    public function rejectProvider($id)
    {

        try {
            $provider = Provider::find($id);
            $this->authorize('rejectProvider', $provider);
            return DB::transaction(function () use ($provider) {
                $provider->update(['status' => 'blocked']);
                $user = $provider->user;
                $user->notify(new ProviderNotification($provider, 'The admin of category has blocked your request to be a service provider'));
                return response()->json([
                    'status' => 1,
                ]);
            });

        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
            ]);
        }
    }

    // Generate a monthly report for services provided by a provider.
    public function reportService($provider)
    {
        try {
            $this->authorize('admin', Provider::find($provider));

            $services = DB::table('providers')
                ->where('provider_id', '=', $provider)
                ->join('service_locations', 'service_locations.provider_id', '=', 'providers.id')
                ->join('services', 'service_locations.id', '=', 'services.service_location_id')
                ->join('appointments', 'services.id', '=', 'appointments.service_id')

                ->select(
                    'services.name',
                    'appointments.status',
                    'appointments.updated_at as date',
                    'providers.bussiness_name as provider_name',
                    DB::raw('SUM(services.price - (services.price * services.discount / 100)) as total_amount')
                )
                ->whereMonth('appointments.created_at', date('m'))->where('appointments.status', '=', 'done')
                ->groupBy(
                    'services.name',
                    'appointments.status',
                    'appointments.updated_at',
                    'providers.bussiness_name'
                )
                ->get();
            return response()->json([
                'status' => 1,
                'sales' => $services
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
            ]);
        }
    }

    // Generate a monthly report for products sold by a provider.
    public function reportProduct($provider)
    {
        try {
            $this->authorize('admin', Provider::find($provider));
            $product[] = DB::table('providers')->where('provider_id', '=', $provider)
                ->join('products', 'products.provider_id', '=', 'providers.id')
                ->join('order_product', 'products.id', '=', 'order_product.product_id')
                ->join('orders', 'order_product.order_id', '=', 'orders.id')
                ->select(
                    'products.name',
                    'order_product.status',
                    'orders.updated_at  as date',
                    'providers.bussiness_name as provider_name',
                    DB::raw('SUM(order_product.quantity * (products.price - (products.price * products.discount / 100))) as total_amount')
                )
                ->whereMonth('orders.created_at', date('m'))->where('order_product.status', '=', 'delivered')

                ->groupBy('products.name', 'order_product.product_id', 'order_product.status', 'orders.updated_at', 'providers.bussiness_name')
                ->get();
            return response()->json([
                'status' => 1,
                'sales' => $product,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
            ]);
        }

    }
}
