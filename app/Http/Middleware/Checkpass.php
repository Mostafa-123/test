<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Checkpass
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->_pass !==env('Api_Pass','000')){
            return response()->json(['message'=>'error']);
        }

        return $next($request);
    }
}
