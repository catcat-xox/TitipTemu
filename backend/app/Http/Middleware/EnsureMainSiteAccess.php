<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureMainSiteAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user('admin') !== null) {
            return redirect()->route('admin.dashboard');
        }

        return $next($request);
    }
}
