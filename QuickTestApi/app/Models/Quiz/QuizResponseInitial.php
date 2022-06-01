<?php

namespace App\Models\Quiz;

use Illuminate\Database\Eloquent\Model;

class QuizResponseInitial extends Model
{
    protected $table='quizResponseInitials';
    protected $primaryKey = 'quizResponseInitialId';
    public $timestamps = false;
    protected $fillable = [
        'userId',    
        'email',
        'attemptCount',    
        'isExamined',
        'quizTopicId',    
        'quizTitle',
        'quizMark',    
        'userObtainedQuizMark',
        'quizTime',    
        'timeTaken',
        'startTime',    
        'endTime',
        'addedBy'
    ];

    protected $hidden = [
        'isMigrationData',
    ];
}
