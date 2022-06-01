<?php

namespace App\Models\Quiz;

use Illuminate\Database\Eloquent\Model;

class QuizTopic extends Model
{
    protected $table='quizTopics';
    protected $primaryKey = 'quizTopicId';
    public $timestamps = false;
    protected $fillable = [
        'quizTitle',    
        'quizTime',
        'quizTotalMarks',    
        'quizPassMarks',
        'quizMarkOptionId',    
        'quizParticipantOptionId',
        'certificateTemplateId',    
        'allowMultipleInputByUser',
        'allowMultipleAnswer',    
        'allowMultipleAttempt',
        'allowCorrectOption',    
        'quizscheduleStartTime',
        'quizscheduleEndTime',
        'quizPrice',
        'isRunning',
        'addedBy'
    ];

    protected $hidden = [
        'isMigrationData',
    ];
}
