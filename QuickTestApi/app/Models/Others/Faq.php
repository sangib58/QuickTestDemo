<?php

namespace App\Models\Others;

use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    protected $table='faqs';
    protected $primaryKey = 'qaqId';
    public $timestamps = false;
    protected $fillable = [
        'title',    
        'description',
        'addedBy'
    ];

    protected $hidden = [
        'isMigrationData',
    ];
}
