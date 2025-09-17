<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'bengkel_id',
        'alamat',
        'no_telp',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->created_by = Auth::id(); // Set created_by to the authenticated user's ID
        });
        static::updating(function ($model) {
            $model->updated_by = Auth::id(); // Set updated_by to the authenticated user's ID
        });
        static::deleting(function ($model) {
            $model->deleted_by = Auth::id(); // Set deleted_by to the authenticated user's ID
            $model->save(); // Save the model to update the deleted_by field
        });
    }
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user'); // Sesuaikan dengan nama tabel pivot Anda
    }

    public function hasRole($role)
    {
        if (is_string($role)) {
            return $this->roles->contains('name', $role);
        }

        return !!$role->intersect($this->roles)->count();
    }

    public function canAccessMenu($menuId)
    {
        if ($this->hasRole('super-admin')) {
            return true;
        }

        return $this->roles()->whereHas('menus', function ($query) use ($menuId) {
            $query->where('menus.id', $menuId);
        })->exists();
    }

    // app/Models/User.php
    public function getAllowedMenuRoutes()
    {
        return DB::table('menus')
            ->join('menu_role', 'menus.id', '=', 'menu_role.menu_id')
            ->join('role_user', 'menu_role.role_id', '=', 'role_user.role_id')
            ->where('role_user.user_id', $this->id)
            ->whereNotNull('menus.route')
            ->pluck('menus.route')
            ->toArray();
    }
    public function getRoleAttribute()
    {
        return $this->roles->first();
    }
}
