<?php

namespace App\Models\Quiz;

use Illuminate\Database\Eloquent\Model;

class QuizResponseDetail extends Model
{
    protected $table='quizResponseDetails';
    protected $primaryKey = 'quizResponseDetailId';
    public $timestamps = false;
    protected $fillable = [
        'quizResponseInitialId',    
        'quizQuestionId',
        'questionDetail',    
        'userAnswer',
        'isAnswerSkipped',    
        'correctAnswer',
        'answerExplanation',    
        'questionMark',
        'userObtainedQuestionMark',    
        'imagePath',
        'videoPath',    
        'isExamined',
        'addedBy'
    ];

    protected $hidden = [
        'isMigrationData',
    ];
}
