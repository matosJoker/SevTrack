<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckMenuAccess
{
    public function handle(Request $request, Closure $next)
    {

        $user = Auth::user();
        $currentRoute = $request->route() ? $request->route()->getName() : null;

        // Skip untuk super admin
        if ($user->hasRole('super-admin')) {
            return $next($request);
        }

        // Cek apakah route ada di menu yang diakses user
        $allowedRoutes = $user->getAllowedMenuRoutes();

        // Ambil prefix sebelum titik, misal 'menus.show' jadi 'menus'
        $currentRoutePrefix = $currentRoute ? explode('.', $currentRoute)[0] : null;

        // Allow access if current route matches any allowed route prefix
        $accessGranted = false;
        foreach ($allowedRoutes as $routePrefix) {
            $routePrefix = $routePrefix ? explode('.', $routePrefix)[0] : null;
            if (strpos($currentRoutePrefix, $routePrefix) === 0) {
                $accessGranted = true;
                break;
            }
        }

        if (!$accessGranted) {
            abort(403, 'Unauthorized access to this menu');
        }

        return $next($request);
    }
}
