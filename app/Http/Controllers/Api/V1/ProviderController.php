<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Models\Api\V1\Provider;
use App\Models\Api\V1\Product;
use App\Models\Api\V1\Service;
use App\Models\Api\V1\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Models\Api\V1\ProviderProfileUpdateRequest;
use App\Http\Requests\Api\V1\UpdateProviderRequest;
use App\Http\Controllers\Controller;
use App\Notifications\Api\V1\AdminNotification;
use Illuminate\Support\Facades\DB;

class ProviderController extends Controller
{

    public function updateRequest(UpdateProviderRequest $request, $id)
    {
        $provider = Provider::find($id);
       try{
        return DB::transaction(function () use ($provider)
            {
                $this->authorize('update',$provider);
                $requestData = $request->all();
                $requestData['provider_id'] = $provider->id;

                $updateRequest = ProviderProfileUpdateRequest::create($requestData);

                $provider->update(['status' => 'pending']);
                $admin=$provider->category->admin->user;
                $admin->notify(new AdminNotification($provider));

                return response()->json([
                    'status' => 1,
                    'provider' => $updateRequest,
                    'message' => 'تم إرسال طلب التحديث بنجاح',

                ]);
            });
       }catch(\Exception $e){
            return response()->json([
                'status' =>0,
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
       try{
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
       }catch(\Exception $e){
        return response()->json([
            'status'=>0,
        ]);
       }
    }
    /** Approve request service provider */
    public function approveProvider($id){
        try
        {
            $provider=Provider::find($id);
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

        try
        {
            $provider=Provider::find($id);
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
 /** monthly reports */
 public function reports($month){

    try{
     Gate::authorize('isAdmin');
     $categories=Category::where('admin_id',Auth::id())->get();
     foreach($categories as $category)
     {    $product[]=DB::table('providers')->where('service_type_id','=',$category->id)->join('products','providers.id','=','products.provider_id')
         ->join('order_product','products.id','=','order_product.product_id')
         ->join('orders','order_product.order_id','=','orders.id')->select('providers.bussiness_name','products.title',
         DB::raw('SUM(order_product.quantity * (products.price - (products.price * products.discount / 100))) as total_amount'))
         ->whereMonth('orders.created_at',$month)->where('order_product.status','=','accepted')
         ->groupBy( 'providers.bussiness_name','products.title')
         ->get();
         $services=DB::table('providers')->join('service_locations','providers.id','=','service_locations.provider_id')->join(
             'services','service_locations.id','=','services.service_location_id'
         )->join('appointments','services.id','=','appointments.service_id')
         ->select('providers.bussiness_name','services.name',
         DB::raw('SUM(services.price - (services.price * services.discount / 100)) as total_amount'))
         ->whereMonth('appointments.created_at',$month)->where('appointments.status','=','done')
         ->groupBy('providers.bussiness_name','services.name')
         ->get();
     }
      return response()->json([
         'products'=>$product,
         'services'=> $services
      ]);
    }catch(\Exception){
     return response()->json([
         'status'=>0
      ]);
    }

      /*   $product=Provider::with(['products.orders'=>function($query) use ($month){
         $query->where('order_product.status','accepted');
         $query->whereMonth('orders.created_at',$month);
     }])->get(); */
 }
}
