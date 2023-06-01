<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Facades\JWTAuth;

class CheckAuth
{
    /**
     * Handle an incoming request. Will return error message 'Unauthorized user' if user not logged in.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $err = '';
        try {
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                $err = 'Invalid request';
            }
        } catch (JWTException $e) {
            $err = $e->getMessage();
        } catch (TokenExpiredException $e){
            $err = $e->getMessage();
        }

        if($err != '') {
            return response()->json([
                'error' => $err,
                'message' => 'Please try to re-login again!',
            ], Response::HTTP_UNAUTHORIZED);
        }
        $request['user-obj'] = $user;
        return $next($request);
    }
}
