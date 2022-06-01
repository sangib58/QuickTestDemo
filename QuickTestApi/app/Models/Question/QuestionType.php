<?php

namespace App\Models\Question;

use Illuminate\Database\Eloquent\Model;

class QuestionType extends Model
{
    protected $table='questionTypes';
    protected $primaryKey = 'questionTypeId';
    public $timestamps = false;
}
