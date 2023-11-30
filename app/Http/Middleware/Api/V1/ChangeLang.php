<?php

namespace App\Http\Middleware\Api\V1;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChangeLang
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        app()->setLocale('en');
        if(isset($request->lang) && $request->lang == 'ar'){
            app()->setLocale('ar');
        }
        if(auth('sanctum')->check()){
          $lang=DB::table('languages')->where('user_id','=',auth('sanctum')->id())->first();
          if($lang)
         { app()->setLocale($lang->lang);}
        }
        return $next($request);
    }
}
