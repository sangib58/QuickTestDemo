<?php

namespace App\Models\User;

//use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Users extends Authenticatable
{
    use HasFactory,Notifiable,HasApiTokens;

    protected $table='users';
    protected $primaryKey = 'userId';
    public $timestamps = false;
    protected $fillable = [
        'userRoleId',    
        'fullName',   
        'mobile',
        'email',
        'password',
        'address',    
        'dateOfBirth',   
        'imagePath',
        'addedBy'
    ];

    protected $hidden = [
        'isMigrationData'
    ];
}
