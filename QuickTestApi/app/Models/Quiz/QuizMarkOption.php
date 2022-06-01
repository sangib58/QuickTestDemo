<?php

namespace App\Models\Quiz;

use Illuminate\Database\Eloquent\Model;

class QuizMarkOption extends Model
{
    protected $table='quizMarkOptions';
    protected $primaryKey = 'quizMarkOptionId';
    public $timestamps = false;
}
