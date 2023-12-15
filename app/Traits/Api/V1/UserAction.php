<?php
namespace App\Traits\Api\V1;

use Illuminate\Support\Facades\Cache;
use Illuminate\Auth\Events\Registered;

trait UserAction
{
    // Reset the email verification status of a user.
    public function resetEmailVerification($user)
    {
        // Set the email verification status to null
        $user->email_verified_at = null;

        // Generate a verification code
        $verificationCode = mt_rand(100000, 999999);

        // Store the verification code in the cache for 60 minutes
        Cache::put('verification_code:' . $user->id, $verificationCode, 60);

        // Trigger the Registered event to send the verification email
        event(new Registered($user));
    }
}