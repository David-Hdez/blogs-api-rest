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
            $response=array(
                'status'=>'error',
                'code'=>409,
                'message'=>'Subiendo imagen'
            );

            return response()->json($response, $response['code']);
        }                
    }
}