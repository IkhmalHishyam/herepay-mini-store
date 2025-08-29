<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleRedirect
{
        public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        
        if ($user->role->value === \App\Enums\UserRoleEnum::CUSTOMER->value)
        {
            return redirect()->route('home');
        }
        
        return $next($request);
    }
}
