<?php

namespace App\Models\Others;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $table='siteSettings';
    protected $primaryKey = 'siteSettingsId';
    public $timestamps = false;
}
