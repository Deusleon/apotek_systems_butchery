<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use Notifiable, HasRoles;

    protected $connection = 'apotek_updated';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function __construct()
    {
        parent::__construct();
        $this->connection = session()->get('db_connection');
    }

    public function stockTracking()
    {
        return $this->hasMany(StockTracking::class, 'updated_by');
    }

    public function stockAdjustment()
    {
        return $this->hasMany(StockAdjustment::class, 'created_by');
    }

    public function stockIssue()
    {
        return $this->hasMany(StockIssue::class, 'updated_by');
    }

    public function issueReturn()
    {
        return $this->hasMany(IssueReturn::class, 'returned_by');
    }

    public function sale()
    {
        return $this->hasOne(Sale::class, 'created_by');
    }

    public function expense()
    {
        return $this->hasMany(Expense::class, 'updated_by');
    }

    public function order()
    {
        return $this->hasMany(Order::class, 'ordered_by');
    }

    public function goodsReceiving()
    {
        return $this->hasMany(GoodsReceiving::class, 'created_by');
    }

    public function checkPermission($permission_name)
    {

        $permission = DB::table('role_has_permissions')
            ->join('model_has_roles', 'model_has_roles.role_id', '=', 'role_has_permissions.role_id')
            ->join('permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
            ->where('permissions.name', $permission_name)
            ->where('model_id', Auth::user()->id)
            ->get();

        if ($permission->isEmpty()) {
            return false;
        } else {
            return true;
        }
    }

    public function isAdmin($role_name)
    {

        $role = DB::table('roles')
            ->join('model_has_roles', 'model_has_roles.role_id', 'roles.id')
            ->where('roles.name', $role_name)
            ->where('model_id', Auth::user()->id)
            ->get();

        if ($role->isEmpty()) {
            return false;
        } else {
            return true;
        }
    }

}
