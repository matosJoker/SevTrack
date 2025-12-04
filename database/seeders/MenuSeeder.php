<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Role;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run()
    {
        // Get or create roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $mekanikRole = Role::firstOrCreate(['name' => 'mekanik']);

        // Define all menus in a structured array
        $menus = [
            [
                'name' => 'Dashboard',
                'icon' => 'fas fa-tachometer-alt',
                'route' => 'dashboard',
                'parent_id' => null,
                'order' => 0,
                'is_active' => true,
                'roles' => [$adminRole->id]
            ],
            [
                'name' => 'ACL Management',
                'icon' => 'fas fa-user-tag',
                'route' => null,
                'parent_id' => null,
                'order' => 1,
                'is_active' => true,
                'roles' => [$adminRole->id],
                'children' => [
                    [
                        'name' => 'Roles',
                        'icon' => 'fas fa-list',
                        'route' => 'roles.index',
                        'order' => 1,
                        'roles' => [$adminRole->id]
                    ],
                    [
                        'name' => 'Permissions',
                        'icon' => 'fas fa-shield-alt',
                        'route' => 'permissions.index',
                        'order' => 2,
                        'roles' => [$adminRole->id]
                    ],
                    [
                        'name' => 'Menus',
                        'icon' => 'fas fa-bars',
                        'route' => 'menus.index',
                        'order' => 3,
                        'roles' => [$adminRole->id]
                    ]
                ]
            ],
            [
                'name' => 'Master Data',
                'icon' => 'fas fa-database',
                'route' => null,
                'parent_id' => null,
                'order' => 2,
                'is_active' => true,
                'roles' => [$adminRole->id],
                'children' => [
                    [
                        'name' => 'Users',
                        'icon' => 'fas fa-users',
                        'route' => 'user.index',
                        'order' => 1,
                        'roles' => [$adminRole->id]
                    ],
                    [
                        'name' => 'Bengkel',
                        'icon' => 'fas fa-tools',
                        'route' => 'bengkel.index',
                        'order' => 2,
                        'roles' => [$adminRole->id]
                    ],
                    [
                        'name' => 'Pekerjaan',
                        'icon' => 'fas fa-cogs',
                        'route' => 'layanan.index',
                        'order' => 3,
                        'roles' => [$adminRole->id]
                    ],
                    [
                        'name' => 'Service Advisor',
                        'icon' => 'fas fa-user-tie',
                        'route' => 'serviceadvisor.index',
                        'order' => 4,
                        'roles' => [$adminRole->id]
                    ]
                ]
            ],
            [
                'name' => 'Transaksi',
                'icon' => 'fas fa-hand-holding-usd',
                'route' => 'transactions.index',
                'parent_id' => null,
                'order' => 3,
                'is_active' => true,
                'roles' => [$mekanikRole->id]
            ],
            [
                'name' => 'Laporan Transaksi',
                'icon' => 'fas fa-file-alt',
                'route' => 'transactions.report',
                'parent_id' => null,
                'order' => 4,
                'is_active' => true,
                'roles' => [$mekanikRole->id]
            ],
            [
                'name' => 'Laporan',
                'icon' => 'fas fa-file-alt',
                'route' => null,
                'parent_id' => null,
                'order' => 5,
                'is_active' => true,
                'roles' => [$adminRole->id],
                'children' => [
                    [
                        'name' => 'Data Pelanggan',
                        'icon' => 'fas fa-user',
                        'route' => 'laporan.pelanggan',
                        'order' => 1,
                        'roles' => [$adminRole->id]
                    ],
                    [
                        'name' => 'Data Transaksi',
                        'icon' => 'fas fa-file-alt',
                        'route' => 'laporan.transaksi',
                        'order' => 2,
                        'roles' => [$adminRole->id]
                    ]
                ]
            ],
        ];

        // Create menus recursively
        $this->createMenus($menus);
    }

    /**
     * Recursively create menus and attach roles
     *
     * @param array $menus
     * @param int|null $parentId
     */
    private function createMenus(array $menus, ?int $parentId = null): void
    {
        foreach ($menus as $menuData) {
            // Extract children and roles from menu data
            $children = $menuData['children'] ?? [];
            $roles = $menuData['roles'] ?? [];
            unset($menuData['children'], $menuData['roles']);

            // Set parent_id if provided
            if ($parentId) {
                $menuData['parent_id'] = $parentId;
            }

            // Create the menu
            $menu = Menu::create($menuData);

            // Attach roles if any
            if (!empty($roles)) {
                $menu->roles()->attach($roles);
            }

            // Create children recursively if any
            if (!empty($children)) {
                $this->createMenus($children, $menu->id);
            }
        }
    }
}
