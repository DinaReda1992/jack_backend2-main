<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $guard)
    {
        if ($guard == 'driver' && auth('api')->user()->user_type_id != 6) {
            return response()->json(['messages' => __('messages.You do not have the authority to do this')], 400);
        }
        if ($guard == 'warehouse' && auth('api')->user()->user_type_id != 2) {
            return response()->json(['messages' => __('messages.You do not have the authority to do this')], 400);
        }
        return $next($request);
    }
}
