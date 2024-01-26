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
use App\Http\Requests\Api\V1\ChangeLanguage;
use App\Traits\Api\V1\PDFs;
use Illuminate\Support\Facades\Auth;


class AuthController extends Controller
{

    //  Register a patient.
    public function registerPatient(RegisterPatientRequest $request)
    {

        $user = User::create([
            'email' => $request['email'],
            'password' => $request['password'],
            'role' => 'patient',
        ]);

        $this->EmailVerification($user);
        // event(new Registered($user));

        // $token = $user->createToken('register_token')->plainTextToken;
        $token = $user->createToken(
            'register_token',
            ['*'],
            now()->addHours(5)
        )->plainTextToken;

        return response()->json([
            'status' => 1,
            'user' => $user,
            'token' => $token,
            'message' => 'تم إرسال رمز التحقق إلى بريدكم'
        ]);
    }

    // Generate an email verification code and send it to the user.
    private function EmailVerification($user)
    {
        $verificationCode = mt_rand(100000, 999999);

        Cache::put('verification_code:' . $user->id, $verificationCode, now()->addHour());

        event(new Registered($user));
    }

    // Confirm the email verification code.
    public function confirmVerificationCode(VerifyRequest $request)
    {
        $user = $request->user();
        $verificationCode = Cache::get('verification_code:' . $user->id);

        if ($verificationCode && $verificationCode == $request['code']) {
            if ($user->update(['email_verified_at' => now()])) {

                Cache::forget('verification_code:' . $user->id);

                return response()->json([
                    'status' => 1,
                    'message' => 'تم تأكيد البريد الإلكتروني بنجاح.',
                ]);
            }
        }

        return response()->json([
            'status' => 0,
            'message' => 'كود التحقق غير صحيح.',
        ]);
    }

    // Login a user.
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (auth()->attempt($credentials)) {
            $user = auth()->user();
            // $token = $request->user()->createToken('login_token')->plainTextToken;
            $token = $user->createToken(
                'login_token',
                ['*'],
                now()->addHours(5)
            )->plainTextToken;

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

    // Logout a user.
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

    // Refresh the user's token.
    public function refresh(Request $request)
    {
        $user = $request->user();
        $user->tokens()->delete();
        $token = $user->createToken('api_token')->plainTextToken;

        return response()->json([
            'status' => 1,
            'user' => $user,
            'token' => $token,
            'message' => 'تم تحديث بيانات الاعتماد بنجاح',
        ]);
    }

    // Register a provider.
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

            // $verificationCode = mt_rand(100000, 999999);
            // Cache::put('verification_code:' . $user->id, $verificationCode, 60);

            // event(new Registered($user));
            $this->EmailVerification($user);


            // $token = $user->createToken('register_token')->plainTextToken;
            $token = $user->createToken(
                'register_token',
                ['*'],
                now()->addHours(5)
            )->plainTextToken;



            return response()->json([
                'status' => 1,
                'user' => $user,
                'provider' => $provider,
                'token' => $token,
                'message' => 'تم إرسال رمز التحقق إلى بريدكم',
            ]);
        });
    }

    // Change the user's language.
    function changeLang(ChangeLanguage $req)
    {
        $user = DB::table('languages')->where('user_id', Auth::id())->first();
        if ($req->lang == 'ar' && !$user) {
            DB::table('languages')->insert([
                'user_id' => Auth::id(),
                'lang' => $req->lang,
            ]);
        } else if ($req->lang == 'en' && $user) {
            DB::table('languages')->where('user_id', Auth::id())->delete();
        }

        return response()->json([
            'status' => 1,
            'message' => 'Language changed successfully.',
        ]);
    }
}
