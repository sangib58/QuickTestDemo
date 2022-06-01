<?php

namespace App\Models\Question;

use Illuminate\Database\Eloquent\Model;

class QuestionCategory extends Model
{
    protected $table='questionCategories';
    protected $primaryKey = 'questionCategoryId';
    public $timestamps = false;
    protected $fillable = [
        'questionCategoryName',    
        'addedBy'
    ];
    protected $hidden = [
        'isMigrationData',
    ];
}
