<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ApiAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('Authorization');
        $Authenticate = true;

        if(!$token) {
            $Authenticate = false;
        }

        $user = User::where('token', $token)->first();
        if(!$user) {
            $Authenticate = false;
        }else{
            Auth::login($user);
        }


        if($Authenticate) {
            return $next($request);
        }else {
            return response()->json([
                'errors' => [
                    'message'=> [
                        'Unauthorized'
                    ]
                ]
            ])->setStatusCode(401);
        }
    }
}
