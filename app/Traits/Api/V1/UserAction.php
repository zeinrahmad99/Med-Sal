<?php
namespace App\Traits\Api\V1;
use Illuminate\Support\Facades\Cache;
use Illuminate\Auth\Events\Registered;

trait UserAction
{
    public function resetEmailVerification($user)
    {
        $user->email_verified_at = null;

        $verificationCode = mt_rand(100000, 999999);
        Cache::put('verification_code:' . $user->id, $verificationCode, 60);

        event(new Registered($user));
    }
}