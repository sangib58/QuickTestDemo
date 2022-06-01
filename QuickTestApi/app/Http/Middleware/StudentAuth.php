<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::guard('sanctum')->check() && $request->user()->userRoleId == 2) {
            return $next($request);
        } else {
            $message = ["message" => "Permission Denied"];
            return response($message, 403);
        }
    }
}
