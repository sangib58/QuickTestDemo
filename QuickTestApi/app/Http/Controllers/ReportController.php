<?php

namespace App\Http\Controllers;

use App\Models\Quiz\QuizResponseInitial;
use App\Models\Quiz\QuizResponseDetail;
use App\Models\Quiz\QuizTopic;
use App\Models\Quiz\ReportType;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ReportController extends Controller
{
    use ApiResponser;

    public function GetFinishedExamInfo($id)
    {
        try
        {
            $data=QuizResponseInitial::where('quizResponseInitialId',$id)->first();
            return $data;
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }

    public function GetFinishedExamResult($id)
    {
        try
        {
            $data=DB::table('quizresponseinitials')
            ->leftjoin('users','users.userId','=','quizresponseinitials.userId')
            ->leftjoin('quiztopics','quiztopics.quizTopicId','=','quizresponseinitials.quizTopicId')
            ->leftjoin('quizresponsedetails','quizresponsedetails.quizResponseInitialId','=','quizresponseinitials.quizResponseInitialId')

            ->select('users.fullName','users.email','users.mobile','users.address','quizresponseinitials.attemptCount',
            'quizresponseinitials.quizTitle','quizresponseinitials.quizMark','quizresponseinitials.quizTime','quizresponseinitials.userObtainedQuizMark',
            'quizresponseinitials.timeTaken','quizresponsedetails.questionDetail','quizresponsedetails.userAnswer','quizresponsedetails.isAnswerSkipped',
            'quizresponsedetails.correctAnswer','quizresponsedetails.answerExplanation','quizresponsedetails.questionMark',
            'quizresponsedetails.userObtainedQuestionMark','quiztopics.certificateTemplateId','quiztopics.allowCorrectOption',
            'quiztopics.quizPassMarks','quiztopics.quizMarkOptionId')

            ->where('quizresponseinitials.quizResponseInitialId',$id)
            ->get();
            return $data;
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }

    public function GetPendingExamine($id)
    {
        try
        {
            $data=DB::table('quizresponsedetails')
            ->join('quizresponseinitials','quizresponsedetails.quizResponseInitialId','=','quizresponseinitials.quizResponseInitialId')
            ->join('users','users.userId','=','quizresponseinitials.userId')
            ->join('quizquestions','quizquestions.quizQuestionId','=','quizresponsedetails.quizQuestionId')

            ->select('users.fullName','users.email','users.mobile','users.address','quizresponseinitials.attemptCount',
            'quizresponseinitials.quizTitle','quizresponseinitials.quizMark','quizresponseinitials.quizTime','quizresponseinitials.userObtainedQuizMark',
            'quizresponseinitials.timeTaken','quizresponsedetails.quizResponseDetailId','quizresponsedetails.quizQuestionId','quizresponsedetails.questionDetail',
            'quizresponsedetails.userAnswer','quizresponsedetails.isAnswerSkipped','quizresponsedetails.correctAnswer','quizresponsedetails.answerExplanation',
            'quizresponsedetails.questionMark','quizresponsedetails.userObtainedQuestionMark')

            ->where([
                ['quizresponseinitials.quizResponseInitialId',$id],
                ['quizquestions.questionTypeId',2],
                ['quizresponsedetails.isAnswerSkipped',false],
                ['quizresponsedetails.isExamined',false]
            ])
            ->get();
            return $data;
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }

    public function UpdateMarksObtain($id,$marks)
    {
        try
        {
            DB::beginTransaction();

            $objDetail=QuizResponseDetail::where('quizResponseDetailId',$id)->first();
            QuizResponseDetail::where('quizResponseDetailId',$id)
            ->update([
                'userObtainedQuestionMark'=>$marks,
                'isExamined'=>true
            ]);

            $sumOfQuizMark=0;
            $sumOfQuizMark=QuizResponseDetail::where('quizResponseInitialId',$objDetail->quizResponseInitialId)->sum('userObtainedQuestionMark');
            QuizResponseInitial::where('quizResponseInitialId',$objDetail->quizResponseInitialId)
            ->update([
                'userObtainedQuizMark'=>$sumOfQuizMark
            ]);

            if(QuizResponseDetail::where([
                ['quizResponseInitialId',$objDetail->quizResponseInitialId],
                ['isExamined',false]
            ])->count()==0){
                QuizResponseInitial::where('quizResponseInitialId',$objDetail->quizResponseInitialId)
                ->update([
                    'isExamined'=>true
                ]);
            }

            DB::commit();
            return $this->successMsg(strval($objDetail->quizResponseInitialId));
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }

    public function GetResults()
    {
        try
        {
            $data=DB::table('quizresponseinitials')
            ->join('users','users.userId','=','quizresponseinitials.userId')
            ->join('quiztopics','quiztopics.quizTopicId','=','quizresponseinitials.quizTopicId')
            ->select('users.fullName','users.email','users.mobile','users.address','quizresponseinitials.attemptCount','quizresponseinitials.quizResponseInitialId',
            'quizresponseinitials.quizTitle','quizresponseinitials.quizMark','quizresponseinitials.quizTime','quizresponseinitials.userObtainedQuizMark',
            'quizresponseinitials.timeTaken','quizresponseinitials.isExamined','quizresponseinitials.dateAdded','quiztopics.certificateTemplateId','quiztopics.allowCorrectOption',
            'quiztopics.quizPassMarks','quiztopics.quizMarkOptionId','quiztopics.quizTopicId')
            ->orderBy('quizresponseinitials.dateAdded','desc')
            ->get();
            return $data;
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }

    public function GetResultsById($id)
    {
        try
        {
            $data=DB::table('quizresponseinitials')
            ->join('users','users.userId','=','quizresponseinitials.userId')
            ->join('quiztopics','quiztopics.quizTopicId','=','quizresponseinitials.quizTopicId')

            ->select('users.fullName','users.email','users.mobile','users.address','quizresponseinitials.attemptCount','quizresponseinitials.quizResponseInitialId',
            'quizresponseinitials.quizTitle','quizresponseinitials.quizMark','quizresponseinitials.quizTime','quizresponseinitials.userObtainedQuizMark',
            'quizresponseinitials.timeTaken','quizresponseinitials.isExamined','quizresponseinitials.dateAdded','quiztopics.certificateTemplateId','quiztopics.allowCorrectOption',
            'quiztopics.quizPassMarks','quiztopics.quizMarkOptionId','quiztopics.quizTopicId')

            ->where([
                ['quizresponseinitials.userId',$id],
                ['quizresponseinitials.isExamined',true]
            ])
            ->orderBy('quizresponseinitials.dateAdded','desc')
            ->get();
            return $data;
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }

    public function GetFilteredQuiz($id)
    {
        try
        {
            $data=QuizResponseInitial::where([
                ['userId',$id],
                ['isExamined',true]
            ])->distinct()->get();
            
            return $data;
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }

    public function GetResultByTopic($id)
    {
        try
        {
            $data=DB::table('quizresponseinitials')
            ->join('users','users.userId','=','quizresponseinitials.userId')

            ->select('users.fullName','users.email','users.mobile','users.address','quizresponseinitials.attemptCount','quizresponseinitials.quizTopicId','quizresponseinitials.quizResponseInitialId',
            'quizresponseinitials.quizTitle','quizresponseinitials.quizMark','quizresponseinitials.quizTime','quizresponseinitials.userObtainedQuizMark',
            'quizresponseinitials.timeTaken')

            ->where('quizresponseinitials.quizTopicId',$id)
            ->orderBy('quizresponseinitials.userId','asc')
            ->get();
            return $data;
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }

    public function GetReportType()
    {
        try
        {
            $data=ReportType::get();
            return $data;
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }
}
