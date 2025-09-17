<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BengkelController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\LayananController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServiceAdvisorController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;

// Redirects based on auth state
Route::redirect('/', '/dashboard')->middleware('auth');
Route::redirect('/', '/login')->middleware('guest');

// Authentication routes
Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('login');
    Route::post('/login', 'login');
    Route::post('/logout', 'logout')->name('logout');
});

// Dashboard route
Route::middleware('auth')->get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::middleware('auth')->get('/dashboard/chart-data', [DashboardController::class, 'getChartData'])->name('dashboard.chart-data');
// Admin routes with common middleware
Route::middleware(['auth', 'verified', 'menu.access'])
    ->prefix('admin')
    ->group(function () {

        // Roles routes with custom actions
        Route::resource('roles', RoleController::class)->except(['show']);
        Route::get('roles/{role}', [RoleController::class, 'show'])->name('roles.show');
        Route::prefix('roles/{roleId}')->group(function () {
            Route::get('assign-user', [RoleController::class, 'assignUserForm'])->name('roles.assign-user-form');
            Route::post('assign-user', [RoleController::class, 'assignUser'])->name('roles.assign-user');
            Route::delete('remove-user/{userId}', [RoleController::class, 'removeUser'])->name('roles.remove-user');
        });

        // Permissions routes with custom actions
        Route::resource('permissions', PermissionController::class)->except(['show']);
        Route::get('permissions/{permission}', [PermissionController::class, 'show'])->name('permissions.show');
        Route::prefix('permissions/{permissionId}')->group(function () {
            Route::get('assign-role', [PermissionController::class, 'assignToRoleForm'])->name('permissions.assign-role-form');
            Route::post('assign-role', [PermissionController::class, 'assignToRole'])->name('permissions.assign-role');
            Route::delete('remove-role/{roleId}', [PermissionController::class, 'removeFromRole'])->name('permissions.remove-role');
        });

        // Menus routes with custom actions
        Route::resource('menus', MenuController::class)->except(['show']);
        Route::get('menus/{menu}', [MenuController::class, 'show'])->name('menus.show');
        Route::prefix('menus/{menuId}')->group(function () {
            Route::get('assign-role', [MenuController::class, 'assignToRoleForm'])->name('menus.assign-role-form');
            Route::post('assign-role', [MenuController::class, 'assignToRole'])->name('menus.assign-role');
            Route::delete('remove-role/{roleId}', [MenuController::class, 'removeFromRole'])->name('menus.remove-role');
        });
    });

Route::middleware(['auth', 'verified', 'menu.access'])
    ->group(function () {
        Route::resource('bengkel', BengkelController::class)->except(['show']);
        Route::get('bengkel/{bengkel}', [BengkelController::class, 'show'])->name('bengkel.show');
        Route::post('bengkel', [BengkelController::class, 'store'])->name('bengkel.store');

        Route::resource('user', UserController::class)->except(['show']);
        Route::get('user/{user}', [UserController::class, 'show'])->name('user.show');
        Route::post('user', [UserController::class, 'store'])->name('user.store');
        Route::get('user/{user}/reset', [UserController::class, 'resetPassword'])->name('user.reset');

        Route::resource('layanan', LayananController::class)->except(['show']);
        Route::get('layanan/{layanan}', [LayananController::class, 'show'])->name('layanan.show');
        Route::post('layanan', [LayananController::class, 'store'])->name('layanan.store');
        Route::post('layanan/status', [LayananController::class, 'changeStatus'])->name('layanan.status');

        Route::resource('serviceadvisor', ServiceAdvisorController::class)->except(['show']);
        Route::get('serviceadvisor/{serviceadvisor}', [ServiceAdvisorController::class, 'show'])->name('serviceadvisor.show');
        Route::post('serviceadvisor', [ServiceAdvisorController::class, 'store'])->name('serviceadvisor.store');

        Route::resource('transactions', TransactionController::class)->except(['show']);
        Route::get('/transactions/report', [TransactionController::class, 'report'])->name('transactions.report');
        Route::get('/transactions/create', [TransactionController::class, 'create'])->name('transactions.create');
        Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');
        Route::get('/transactions/find-by-plat', [TransactionController::class, 'findByPlat'])->name('transactions.findByPlat');
        Route::get('/transactions/{transaction}', [TransactionController::class, 'show'])->name('transactions.show');
        Route::get('/transactions/{transaction}/edit', [TransactionController::class, 'edit'])->name('transactions.edit');
        Route::put('/transactions/{transaction}', [TransactionController::class, 'update'])->name('transactions.update');
        Route::post('/transactions/{transaction}/cancel', [TransactionController::class, 'cancel'])->name('transactions.cancel');

        Route::resource('laporan', LaporanController::class)->except(['show']);
        Route::get('/laporan/report', [LaporanController::class, 'pelanggan'])->name('laporan.pelanggan');
        Route::get('/laporan/create', [LaporanController::class, 'transaksi'])->name('laporan.transaksi');
    });
Route::middleware(['auth', 'verified'])
    ->prefix('profile')
    ->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('profile.index');
        Route::put('/', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
    });
