<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Models\Api\V1\Service;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\CreateServiceRequest;
use App\Http\Requests\Api\V1\UpdateServiceRequest;
use Illuminate\Support\Facades\Gate;
use App\Notifications\Api\V1\activeNotification;
use Illuminate\Support\Facades\DB;

class ServiceController extends Controller
{
    // Get all services.
    public function index()
    {
        if (app()->getLocale() == 'ar') {
            $services = Service::select('category_id', 'service_location_id', 'name_' . app()->getLocale(), 'description_' . app()->getLocale(), 'time_requested', 'price', 'discount', 'status', 'created_at', 'updated_at')->get();
            ;
        } else {
            $services = Service::select('category_id', 'service_location_id', 'name', 'description', 'time_requested', 'price', 'discount', 'status', 'created_at', 'updated_at')->get();
            ;

        }

        return response()->json([
            'status' => $services ? 1 : 0,
            'services' => $services,
        ]);
    }

    // Get a specific service by ID.
    public function show($id)
    {
        if (app()->getLocale() == 'ar') {
            $service = Service::firstWhere('id', $id)->select('category_id', 'service_location_id', 'name_' . app()->getLocale(), 'description_' . app()->getLocale(), 'time_requested', 'price', 'discount', 'status', 'created_at', 'updated_at')->first();
        } else {
            $service = Service::firstWhere('id', $id)->select('category_id', 'service_location_id', 'name', 'description', 'time_requested', 'price', 'discount', 'status', 'created_at', 'updated_at')->first();
        }

        return response()->json([
            'status' => $service ? 1 : 0,
            'service' => $service,
        ]);
    }

    // Create a new service.
    public function store(CreateServiceRequest $request)
    {
        try {
            $this->authorize('create', Service::class);

            $data = array_merge($request->all(), ['status' => 'pending']);

            $service = Service::create($data);

            $admin = $service->category->admin->user;

            $admin->notify(new activeNotification($service, 'new request for service'));

            return response()->json([
                'status' => 1,
                'service' => $service,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
            ]);
        }
    }

    // Update a service.
    public function update(UpdateServiceRequest $request, $id)
    {
        try {
            $service = Service::find($id);
            $this->authorize('update', $service);

            $data = $request->except('status');

            $data = $request->all();

            $service->update($data);

            return response()->json([
                'status' => 1,
                'service' => $service,
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'status' => 0,
            ]);
        }
    }

    // Delete a service.
    public function delete(int $id)
    {

        try {
            $service = Service::firstWhere('id', $id);
            $this->authorize('forceDelete', $service);

            if (!$service) {
                return response()->json([
                    'status' => 0,
                    'message' => 'عذراً يوجد خطأ ما'
                ]);
            }

            $service = $service->delete();

            if (!$service) {
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

    // remove service -> status will be pending

    public function remove($id)
    {

        try {
            $service = Service::findOrfail($id);
            $this->authorize('remove', $service);
            return DB::transaction(function () use ($service) {
                $service->update(['status' => 'unaccept']);
                $user = $service->serviceLocation->provider->user;
                $user->notify(new activeNotification($service, 'The admin of category has made your service un accepted'));

                return response()->json([
                    'status' => 1,
                    'message' => 'remove service successfully'
                ]);
            });


        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
            ]);
        }
    }

    // restore service -> make status active

    public function accepted($id)
    {

        try {
            $service = Service::findOrfail($id);
            $this->authorize('accepted', $service);
            return DB::transaction(function () use ($service) {
                $service->update(['status' => 'active']);
                $user = $service->serviceLocation->provider->user;
                $user->notify(new activeNotification($service, 'The admin of category has made your service activated'));

                return response()->json([
                    'status' => 1,
                    'message' => 'accepted service successfully'
                ]);
            });
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
            ]);
        }
    }


}
