<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;

class SendVerificationEmailListener implements ShouldQueue
{
    use InteractsWithQueue;
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle($event)
    {
        $user = $event->user;

        // Retrieve the verification code from cache
        $verificationCode = Cache::get('verification_code:' . $user->id);

        Mail::send('emails.confirmation', ['verificationCode' => $verificationCode], function ($message) use ($user) {
            $message->to($user->email)->subject('Confirm Your Email');
        });
    }
}
