<?php

namespace App\Models\Quiz;

use Illuminate\Database\Eloquent\Model;

class QuizParticipant extends Model
{
    protected $table='quizParticipants';
    protected $primaryKey = 'quizParticipantId';
    public $timestamps = false;
    protected $fillable = [
        'email',    
        'quizTopicId'
    ];
}
