<?php

namespace App\Http\Middleware;

use Closure;

class JWT
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $token_req=$request->header('Authorization');

        $auth=new \JWTAuth();

        $checkToken=$auth->checkToken($token_req);

        if ($checkToken) {
            return $next($request);
        } else {
            $resp=array(
                'status'=>'error',
                'code'=>403,
                'message'=>'User is not authenticated'
            );

            return response()->json($resp, $resp['code']);
        }                
    }
}
