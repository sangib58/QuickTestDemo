<?php

namespace App\Models\Menu;

use Illuminate\Database\Eloquent\Model;

class MenuMapping extends Model
{
    protected $table='menuMappings';
    protected $primaryKey = 'menuMappingId';
    public $timestamps = false;
    protected $fillable = [
        'userRoleId',    
        'appMenuId',
        'addedBy'
    ];

    protected $hidden = [
        'isMigrationData',
    ];
}
