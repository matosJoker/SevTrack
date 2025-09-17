<?php

namespace App\Http\Middleware;

use App\Services\MenuService;
use Closure;
use Illuminate\Http\Request;

class MenuMiddleware
{
    protected $menuService;

    public function __construct(MenuService $menuService)
    {
        $this->menuService = $menuService;
    }

    public function handle(Request $request, Closure $next)
    {
        if (auth()->check()) {
            $user = auth()->user(); // Tambahkan load() disini
            if ($user->hasRole('super-admin')) {
                $menus = $this->menuService->getAllActiveMenus();
            } else {
                $roleIds = $user->roles->pluck('id')->toArray();
                $menus = !empty($roleIds)
                    ? $this->menuService->getActiveMenusForRoles($roleIds)
                    : collect();
            }

            view()->share('menus', $menus);
        }

        return $next($request);
    }
}
