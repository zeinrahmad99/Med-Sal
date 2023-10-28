<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Api\V1\User;
use App\Models\Api\V1\Provider;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Cache;
use Illuminate\Auth\Events\Registered;
use App\Http\Requests\Api\V1\LoginRequest;
use App\Http\Requests\Api\V1\VerifyRequest;
use App\Http\Requests\Api\V1\RegisterPatientRequest;
use App\Http\Requests\Api\V1\RegisterProviderRequest;

class AuthController extends Controller
{

    public function registerPatient(RegisterPatientRequest $request)
    {
        $data = $request->validated();

        $user = User::create([
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'role' => 'patient',
        ]);

        $verificationCode = mt_rand(100000, 999999);
        Cache::put('verification_code:' . $user->id, $verificationCode, 60);


        event(new Registered($user));

        $token = $user->createToken('api_token')->plainTextToken;

        return response()->json([
            'status' => 1,
            'user' => $user,
            'token' => $token,
            'message' => 'تم إرسال رمز التحقق إلى بريدكم'
        ]);
    }

    public function confirmVerificationCode(VerifyRequest $request)
    {
        $data = $request->validated();

        $userId = $request->user()->id;
        $verificationCode = Cache::get('verification_code:' . $userId);
    
        if ($verificationCode && $verificationCode == $data['code']) {
            $user = User::find($userId);
            $user->email_verified_at = now();
            $user->save();
    
            // Clear the verification code from cache
            Cache::forget('verification_code:' . $userId);
    
            return response()->json([
                'status' => 1,
                'message' => 'تم تأكيد البريد الإلكتروني بنجاح.',
            ]);
        }
    
        return response()->json([
            'status' => 0,
            'message' => 'كود التحقق غير صحيح.',
        ]);
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
    
        if (auth()->attempt($credentials)) {
            $user = auth()->user();
            $token = $request->user()->createToken( 'auth_token' )->plainTextToken;
    
            return response()->json([
                'status' => 1,
                'user' => $user,
                'token' => $token,
                'message' => 'تم تسجيل الدخول بنجاح',
            ]);
        }
    
        return response()->json([
            'status' => 0,
            'message' => 'بيانات الاعتماد غير صحيحة.',
        ]);
    }

public function logout( Request $request ) {

    if ( $request->user()->tokens()->delete() ) {
        return response()->json( [
            'status' => 1,
        ] );
    }

    return response()->json( [
        'status' => 0,
    ] );
}

public function refresh(Request $request)
{
    $user = $request->user();
    $token = $user->createToken('api_token')->plainTextToken;

    return response()->json([
        'status' => 1,
        'user' => $user,
        'token' => $token,
        'message' => 'تم تحديث بيانات الاعتماد بنجاح',
    ]);
}

    public function registerProvider(RegisterProviderRequest $request)
    {
        $data = $request->validated();


        // Create a new user
        $user = User::create([
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'role' => 'provider',
        ]);

        // Create a new provider
        $provider = Provider::create([
            'user_id' => $user->id,
            'service_type_id' => $data['service_type_id'],
            'bussiness_name' => $data['business_name'],
            'contact_number' => $data['contact_number'],
            'bank_name' => $data['bank_name'],
            'iban' => $data['iban'],
            'swift_code' => $data['swift_code'],
            'document' => $data['document'],
            'status' => 'pending',
        ]);

        $verificationCode = mt_rand(100000, 999999);
        Cache::put('verification_code:' . $user->id, $verificationCode, 60);

        event(new Registered($user));

        $token = $user->createToken('api_token')->plainTextToken;


        return response()->json([
            'status' => 1,
            'user' => $user,
            'provider' => $provider,
            'token' => $token,
            'message' => 'تم إرسال رمز التحقق إلى بريدكم',
        ]);

}
}