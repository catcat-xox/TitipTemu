<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserRole
{
    /**
     * @param  array<int, string>  $roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if ($roles === []) {
            return $next($request);
        }

        $user = $request->user('web');
        $admin = $request->user('admin');
        $isGuest = $user === null && $admin === null;
        $isUser = $user !== null && $user->role !== 'admin';

        $allowed = false;
        foreach ($roles as $role) {
            $allowed = match ($role) {
                'guest' => $isGuest,
                'user' => $isUser,
                'admin' => $admin !== null,
                default => false,
            };

            if ($allowed) {
                break;
            }
        }

        if ($allowed) {
            return $next($request);
        }

        if ($admin !== null && ! $isUser && in_array('user', $roles, true)) {
            abort(403);
        }

        if ($admin !== null) {
            return redirect()->route('admin.dashboard');
        }

        if ($user !== null && in_array('guest', $roles, true)) {
            return redirect()->route('home');
        }

        if (in_array('user', $roles, true)) {
            return redirect()->guest(route('login'));
        }

        if (in_array('admin', $roles, true)) {
            return redirect()->guest(route('admin.login'));
        }

        abort(403);
    }
}
