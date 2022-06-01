<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

class LogHistory extends Model
{
    protected $table='logHistories';
    protected $primaryKey = 'logHistoryId';
    public $timestamps = false;
    protected $fillable = [
        'logCode',    
        'logDate',
        'userId',    
        'logInTime',
        'logOutTime',    
        'ip',
        'browser',    
        'browserVersion',
        'platform'
    ];
}
