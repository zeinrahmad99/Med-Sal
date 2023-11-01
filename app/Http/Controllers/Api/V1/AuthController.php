<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Api\V1\User;
use Illuminate\Http\Request;
use App\Models\Api\V1\Provider;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Auth\Events\Registered;
use App\Http\Requests\Api\V1\LoginRequest;
use App\Http\Requests\Api\V1\VerifyRequest;
use App\Http\Requests\Api\V1\RegisterPatientRequest;
use App\Http\Requests\Api\V1\RegisterProviderRequest;
use App\Traits\Api\V1\PDFs;

class AuthController extends Controller
{

    public function registerPatient(RegisterPatientRequest $request)
    {

        $user = User::create([
            'email' => $request['email'],
            'password' => bcrypt($request['password']),
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

        $userId = $request->user()->id;
        $verificationCode = Cache::get('verification_code:' . $userId);

        if ($verificationCode && $verificationCode == $request['code']) {
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
            $token = $request->user()->createToken('auth_token')->plainTextToken;

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

    public function logout(Request $request)
    {

        if ($request->user()->tokens()->delete()) {
            return response()->json([
                'status' => 1,
            ]);
        }

        return response()->json([
            'status' => 0,
        ]);
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

        return DB::transaction(function () use ($request) {

            // Create a new user
            $user = User::create([
                'email' => $request['email'],
                'password' => $request['password'],
                'role' => 'provider',
            ]);

            // Give PDF files random names
            $pdfName = PDFs::givePDFRandomName($request->file('document'));

            // Create a new provider
            $provider = Provider::create([
                'user_id' => $user->id,
                'service_type_id' => $request['service_type_id'],
                'bussiness_name' => $request['business_name'],
                'contact_number' => $request['contact_number'],
                'bank_name' => $request['bank_name'],
                'iban' => $request['iban'],
                'swift_code' => $request['swift_code'],
                'document' => $pdfName,
                'status' => 'pending',
            ]);

            // Store PDF files in public/documents
            PDFs::storePDF($request->file('document'), $pdfName, 'public/documents/');

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
        });
    }
}
