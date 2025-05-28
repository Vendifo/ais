<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle($request, Closure $next, ...$roles)
    {
        $user = $request->user();

        // если роли передаются как строка "admin|methodist", разбей вручную
        if (count($roles) === 1 && str_contains($roles[0], '|')) {
            $roles = explode('|', $roles[0]);
        }

        if (!$user || !$user->hasAnyRole($roles)) {
            return response()->json([
                'message' => 'Доступ запрещён. Нет нужной роли'
            ], 403);
        }

        return $next($request);
    }

}
