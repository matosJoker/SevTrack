<?php

namespace App\Providers;

use App\Models\Bengkel;
use Illuminate\Support\ServiceProvider;
use App\Repositories\RoleRepository;
use App\Models\Role;
use App\Repositories\UserRepository;
use App\Models\User;
use App\Repositories\MenuRepository;
use App\Models\Menu;
use App\Repositories\PermissionRepository;
use App\Models\Permission;
use App\Repositories\BengkelRepository;
use App\Services\MenuService;
use App\Services\PermissionService;
use App\Services\RoleService;
use App\Services\BengkelService;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Repository Bindings
        $this->app->singleton(RoleRepository::class, fn() => new RoleRepository(new Role()));
        $this->app->singleton(UserRepository::class, fn() => new UserRepository(new User()));
        $this->app->singleton(PermissionRepository::class, fn() => new PermissionRepository(new Permission()));
        $this->app->singleton(MenuRepository::class, fn() => new MenuRepository(new Menu()));
        $this->app->singleton(BengkelRepository::class, fn() => new BengkelRepository(new Bengkel()));

        // Service Bindings
        $this->app->singleton(
            RoleService::class,
            fn($app) => new RoleService(
                $app->make(RoleRepository::class),
                $app->make(UserRepository::class)
            )
        );

        $this->app->singleton(
            PermissionService::class,
            fn($app) => new PermissionService(
                $app->make(PermissionRepository::class),
                $app->make(RoleRepository::class)
            )
        );

        $this->app->singleton(
            MenuService::class,
            fn($app) => new MenuService(
                $app->make(MenuRepository::class),
                $app->make(RoleRepository::class)
            )
        );

        $this->app->singleton(
            BengkelService::class,
            fn($app) => new BengkelService(
                $app->make(BengkelRepository::class)
            )
        );
    }

    public function boot()
    {
        //
    }
}
