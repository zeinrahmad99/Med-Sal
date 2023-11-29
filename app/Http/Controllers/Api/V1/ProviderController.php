<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Models\Api\V1\Product;
use App\Models\Api\V1\Service;
use App\Models\Api\V1\Category;
use App\Models\Api\V1\Provider;
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

    public function updateRequest(UpdateProviderRequest $request, $id)
    {
        $provider = Provider::find($id);
        try {
            return DB::transaction(
                function () use ($provider, $request) {
                    $this->authorize('update',$provider);
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

    public function delete(int $id)
    {

        $provider = Provider::where('id', $id)->first();

        if (!$provider) {
            return response()->json([
                'status' => 0,
                'message' => 'عذراً يوجد خطأ ما'
            ]);
        }

        $provider = $provider->delete();

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

    /* Display the products, services of each service provider */
    public function show($id)
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
    /** Approve request service provider */
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

    /**reject  request service provider */

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
    /** monthly reports */


    public function reportService($provider)
    {

        try {
            $this->authorize('admin', Provider::find($provider));

            $services = DB::table('service_locations')->where('provider_id', '=', $provider)->join(
                'services',
                'service_locations.id',
                '=',
                'services.service_location_id'
            )->join('appointments', 'services.id', '=', 'appointments.service_id')
                ->select(
                    'services.name',
                    DB::raw('SUM(services.price - (services.price * services.discount / 100)) as total_amount')
                )
                ->whereMonth('appointments.created_at', date('m'))->where('appointments.status', '=', 'done')
                ->groupBy('services.name')
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


    public function reportProduct($provider)
    {

        try {
            $this->authorize('admin', Provider::find($provider));
            $product[] = DB::table('products')->where('provider_id', '=', $provider)->join('order_product', 'products.id', '=', 'order_product.product_id')
                ->join('orders', 'order_product.order_id', '=', 'orders.id')->select(
                    'products.name',
                    DB::raw('SUM(order_product.quantity * (products.price - (products.price * products.discount / 100))) as total_amount')
                )
                ->whereMonth('orders.created_at', date('m'))->where('order_product.status', '=', 'delivered')

                ->groupBy('products.name')
                ->get();
            return response()->json([
                'status' => 1,
                'sales' => $product
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
            ]);
        }

    }
}
