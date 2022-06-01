<?php

namespace App\Models\Quiz;

use Illuminate\Database\Eloquent\Model;

class QuizParticipantOption extends Model
{
    protected $table='quizParticipantOptions';
    protected $primaryKey = 'quizParticipantOptionId';
    public $timestamps = false;
}
