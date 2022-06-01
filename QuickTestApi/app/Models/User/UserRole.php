<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    protected $table='userRoles';
    protected $primaryKey = 'userRoleId';
    public $timestamps = false;
    protected $fillable = [
        'roleName',    
        'roleDesc',
        'displayName',
        'addedBy'
    ];

    protected $hidden = [
        'isMigrationData',
    ];
}
