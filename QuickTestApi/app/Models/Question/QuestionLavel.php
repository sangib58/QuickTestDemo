<?php

namespace App\Models\Question;

use Illuminate\Database\Eloquent\Model;

class QuestionLavel extends Model
{
    protected $table='questionLavels';
    protected $primaryKey = 'questionLavelId';
    public $timestamps = false;
}
