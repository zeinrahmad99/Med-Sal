<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Models\Api\V1\Provider;
use App\Models\Api\V1\Product;
use App\Models\Api\V1\Service;
use App\Models\Api\V1\ProviderProfileUpdateRequest;
use App\Http\Requests\Api\V1\UpdateProviderRequest;
use App\Http\Controllers\Controller;
use App\Notifications\Api\V1\ProviderNotification;
use Illuminate\Support\Facades\DB;

class ProviderController extends Controller
{
    public function updateRequest(UpdateProviderRequest $request, $id)
    {
        $provider = Provider::find($id);
        $this->authorize('update',$provider);
        $requestData = $request->all();
        $requestData['provider_id'] = $provider->id;

        $updateRequest = ProviderProfileUpdateRequest::create($requestData);

        $provider->update(['status' => 'pending']);

        return response()->json([
            'status' => 1,
            'provider' => $updateRequest,
            'message' => 'تم إرسال طلب التحديث بنجاح',

        ]);

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
    public function index($id)
    {
        $provider=Provider::find($id);
        $this->authorize('view',$provider);
           $product=$provider->products;
           $p_services=$provider->serviceLocations;
           foreach($p_services as $service){
            $providerService[]=$service->services;
           }
        if(!$p_services->isEmpty()){
         return response()->json([
            'services'=>$providerService,
            'products'=>$product,
        ]);
        }
        else{
            return response()->json([
                'products'=>$product,
            ]);
        }
    }
    /** Approve request service provider */
    public function approveProvider($id){
        $provider=Provider::find($id);
        try
        {
            $this->authorize('approveProvider',$provider);
            return DB::transaction(function () use ($provider)
            {
                $provider->update(['status'=>'active']);

                $user=$provider->user;
                $user->notify(new ProviderNotification($provider,'The admin of category has confirmed your request to be a service provider'));
                return response()->json([
                    'status' =>1,
                ]);
             });
        }catch(\Exception $e){
            return response()->json([
                'status' =>0,
            ]);
        }
    }

    /**reject  request service provider */

    public function rejectProvider($id){
        $provider=Provider::find($id);
        try
        {
            $this->authorize('rejectProvider',$provider);
            return DB::transaction(function () use ($provider)
            {
                $provider->update(['status'=>'blocked']);
                $user=$provider->user;
                $user->notify(new ProviderNotification($provider,'The admin of category has blocked your request to be a service provider'));
                return response()->json([
                    'status' =>1,
                ]);
            });

       }catch(\Exception $e){
        return response()->json([
            'status' =>0,
        ]);
    }
}
}
