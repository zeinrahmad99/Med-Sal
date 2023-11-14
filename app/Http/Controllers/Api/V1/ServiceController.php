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
    public function index()
    {
        $services = Service::all();

        return response()->json([
            'status' => $services ? 1 : 0,
            'services' => $services,
        ]);
    }

    public function show($id)
    {
        $service = Service::firstwhere('id', $id);

        return response()->json([
            'status' => $service ? 1 : 0,
            'service' => $service,
        ]);
    }

    public function store(CreateServiceRequest $request)
    {
       try{
        $this->authorize('create',Service::class);

        $data = array_merge($request->all(), ['status' => 'pending']);

        $service = Service::create($data);

        return response()->json([
            'status' => 1,
            'service' => $service,
        ]);} catch(\Exception $e){
            return response()->json([
                'status'=>0,
            ]);
        }
    }

    public function update(UpdateServiceRequest $request, $id)
    {

        $service = Service::find($id);
        try{
        $this->authorize('update',$service);

        $data = $request->except('status');

        $data = $request->all();

        $service->update($data);

        return response()->json([
            'status' => 1,
            'service' => $service,
        ]);}catch(\Exception $e){

        return response()->json([
            'status'=>0,
        ]);
    }
    }


    public function delete(int $id)
    {
        $service = Service::firstWhere('id', $id);
        try{$this->authorize('forceDelete',$service);

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
        ]);}catch(\Exception $e){
            return response()->json([
                'status' => 0,
            ]);
        }

    }

    /**remove service -> status will be pending*/

    public function remove($id){
        $service=Service::findOrfail($id);
        try{
            $this->authorize('remove',$service);
            return DB::transaction(function () use ($service)
            {
                $service->update(['status'=>'unaccept']);
                $user=$service->serviceLocation->provider->user;
                $user->notify(new activeNotification($service,'The admin of category has made your service un accepted'));

                return response()->json([
                    'status'=>1,
                    'message'=>'remove service successfully'
                ]);
            });


        }catch(\Exception $e){
            return response()->json([
                'status' => 0,
            ]);
        }
    }

    /** restore service -> make status active */

    public function accepted($id){
        $service=Service::findOrfail($id);
        try
        {
            $this->authorize('accepted',$service);
            return DB::transaction(function () use ($service)
            {
                $service->update(['status'=>'active']);
                $user=$service->serviceLocation->provider->user;
                $user->notify(new activeNotification($service,'The admin of category has made your service activated'));

                return response()->json([
                    'status'=>1,
                    'message'=>'accepted service successfully'
                ]);
            });
        }catch(\Exception $e){
            return response()->json([
                'status' => 0,
            ]);
        }
    }


}
