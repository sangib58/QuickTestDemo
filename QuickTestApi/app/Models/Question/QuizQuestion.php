<?php

namespace App\Models\Question;

use Illuminate\Database\Eloquent\Model;

class QuizQuestion extends Model
{
    protected $table='quizQuestions';
    protected $primaryKey = 'quizQuestionId';
    public $timestamps = false;
    protected $fillable = [
        'quizTopicId',    
        'questionDetail',
        'serialNo',
        'perQuestionMark',    
        'questionTypeId',
        'questionLavelId',    
        'questionCategoryId',
        'optionA',    
        'optionB',
        'optionC',    
        'optionD',
        'optionE',    
        'correctOption',
        'answerExplanation',    
        'imagePath',
        'videoPath',    
        'isCodeSnippet',
        'addedBy'
    ];

    protected $hidden = [
        'isMigrationData',
    ];
}
