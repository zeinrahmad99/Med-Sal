<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Models\Api\V1\Provider;
use App\Models\Api\V1\ProviderProfileUpdateRequest;
use App\Http\Requests\Api\V1\UpdateProviderRequest;
use App\Http\Controllers\Controller;

class ProviderController extends Controller
{
    public function updateRequest(UpdateProviderRequest $request, $id)
    {
        $provider = Provider::find($id);

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
}
