<?php

namespace App\Models\Menu;

use Illuminate\Database\Eloquent\Model;

class AppMenu extends Model
{
    protected $table='appMenus';
    protected $primaryKey = 'appMenuId';
    public $timestamps = false;
    protected $fillable = [
        'menuTitle',    
        'url',
        'sortOrder',    
        'iconClass',
        'addedBy'
    ];

    protected $hidden = [
        'isMigrationData'
    ];
}
