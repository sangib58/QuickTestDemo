<?php

namespace App\Models\Quiz;

use Illuminate\Database\Eloquent\Model;

class QuizPayment extends Model
{
    protected $table='quizPayments';
    protected $primaryKey = 'quizPaymentId';
    public $timestamps = false;
    protected $fillable = [
        'quizTopicId',    
        'email',
        'currency',    
        'addedBy'
    ];
}
