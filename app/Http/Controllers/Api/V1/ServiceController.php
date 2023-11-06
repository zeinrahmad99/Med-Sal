<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Models\Api\V1\Service;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\CreateServiceRequest;
use App\Http\Requests\Api\V1\UpdateServiceRequest;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::all();

        return response()->json([
            'status' => 1,
            'services' => $services,
        ]);
    }

    public function show($id)
    {
        $service = Service::where('id', $id)->first();


        return response()->json([
            'status' => 1,
            'service' => $service,
        ]);
    }

    public function store(CreateServiceRequest $request)
    {
        $data = array_merge($request->all(), ['status' => 'pending']);

        $service = Service::create($data);

        return response()->json([
            'status' => 1,
            'service' => $service,
        ]);
    }

    public function update(UpdateServiceRequest $request, $id)
    {

        $service = Service::find($id);

        $data = $request->all();

        $service->update($data);

        return response()->json([
            'status' => 1,
            'service' => $service,
        ]);
    }


    public function delete(int $id)
    {
        $service = Service::where('id', $id)->first();

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

    }

}
