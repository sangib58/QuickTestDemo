<?php

namespace App\Http\Controllers;

use App\Models\User\Users;
use App\Models\User\UserRole;
use App\Models\User\LogHistory;
use App\Models\Quiz\QuizTopic;
use App\Models\Quiz\QuizParticipant;
use App\Models\Quiz\QuizResponseInitial;
use App\Models\Question\QuizQuestion;
use App\Models\Quiz\QuizPayment;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DashboardController extends Controller
{
    use ApiResponser;

    public function GetRunningQuizes($email)
    {
        try
        {
            $availableQuizes=[];
            $availableQuizesFilter=[];
            $listWithQuestionCount=[];

            $quizzes=QuizTopic::where('isRunning',true)->get();
            $listWithOutCustomInput=[];
            foreach($quizzes as $item){
                if($item->quizParticipantOptionId==1){
                    $listWithOutCustomInput[]=$item;
                }
            }
            $customInputsList=[];
            foreach($quizzes as $item){
                $matchedEmail=QuizParticipant::where([
                    ['quizTopicId',$item->quizTopicId],
                    ['email',$email]
                ])->first();
                if($item->quizParticipantOptionId==2 && $matchedEmail!=null){
                    $customInputsList[]=$item;
                }
            }

            //for all students
            foreach($listWithOutCustomInput as $item){
                if($item->quizscheduleStartTime==null && $item->quizscheduleEndTime==null){
                    $availableQuizes[]=$item;
                }
            }

            foreach($listWithOutCustomInput as $item){
                if($item->quizscheduleStartTime!=null && $item->quizscheduleEndTime==null){
                    if(date("Y-m-d")>=$item->quizscheduleStartTime){
                        $availableQuizes[]=$item;
                    }                  
                }
            }

            foreach($listWithOutCustomInput as $item){
                if($item->quizscheduleStartTime!=null && $item->quizscheduleEndTime!=null){
                    if(date("Y-m-d")>=$item->quizscheduleStartTime && date("Y-m-d")<=$item->quizscheduleEndTime){
                        $availableQuizes[]=$item;
                    }                  
                }
            }

            //for custom inputs
            foreach($customInputsList as $item){
                if($item->quizscheduleStartTime==null && $item->quizscheduleEndTime==null){
                    $availableQuizes[]=$item;
                }
            }

            foreach($customInputsList as $item){
                if($item->quizscheduleStartTime!=null && $item->quizscheduleEndTime==null){
                    if(date("Y-m-d")>=$item->quizscheduleStartTime){
                        $availableQuizes[]=$item;
                    }                  
                }
            }

            foreach($customInputsList as $item){
                if($item->quizscheduleStartTime!=null && $item->quizscheduleEndTime!=null){
                    if(date("Y-m-d")>=$item->quizscheduleStartTime && date("Y-m-d")<=$item->quizscheduleEndTime){
                        $availableQuizes[]=$item;
                    }                  
                }
            }

            foreach($availableQuizes as $item){
                if($item->allowMultipleAttempt==true){
                    $availableQuizesFilter[]=$item;
                }else if($item->allowMultipleAttempt==false){
                    $recordCount=QuizResponseInitial::where([
                        ['quizTopicId',$item->quizTopicId],
                        ['email',$email]
                    ])->count();
                    if($recordCount==0){
                        $availableQuizesFilter[]=$item;
                    }
                }
            }

            foreach($availableQuizesFilter as $item){
                $questionsCount=QuizQuestion::where('quizTopicId',$item->quizTopicId)->count();
                $paymentCount=QuizPayment::where([
                    ['quizTopicId',$item->quizTopicId],
                    ['email',$email]
                ])->count();

                $initial=collect($item);
                $initial->put('questionsCount',$questionsCount);
                $initial->put('paymentCount',$paymentCount);
                $listWithQuestionCount[]=$initial;
            }

            return $listWithQuestionCount;
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }

    public function GetStatus()
    {
        try
        {
            $totalStudents=Users::where('userRoleId',2)->count();
            $totalQuizes=QuizTopic::count();
            $liveQuizes=QuizTopic::where('isRunning',true)->count();
            $totalQuestions=QuizQuestion::count();

            $data=['totalStudents'=>$totalStudents,'totalQuizes'=>$totalQuizes,
            'liveQuizes'=>$liveQuizes,'totalQuestions'=>$totalQuestions,];
            return $data;
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }

    public function GetLogInSummaryByDate($id)
    {
        try
        {
            $objRole=Users::findOrFail($id);

            if($objRole->userRoleId==1){
                $data=DB::table('loghistories')
                ->select(DB::raw('count(*) as count,cast(logDate as date) date'))
                ->orderBy('logDate','desc')
                ->groupBy(DB::raw('cast(logDate as date)'))
                ->take(10)
                ->get();
                return $this->successData($data);
            }else{
                $data=DB::table('loghistories')
                ->select(DB::raw('count(*) as count,cast(logDate as date) date'))
                ->where('userId',$id)
                ->orderBy('logDate','desc')
                ->groupBy(DB::raw('cast(logDate as date)'))
                ->take(10)
                ->get();
                return $this->successData($data);
            }
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }

    public function GetLogInSummaryByMonth($id)
    {
        try
        {
            $objRole=Users::findOrFail($id);

            if($objRole->userRoleId==1){
                $data=DB::table('loghistories')
                ->select(DB::raw('count(*) as count,MONTH(logDate) as month'))
                ->orderBy('logDate','desc')
                ->groupBy(DB::raw('MONTH(logDate)'))
                ->get();
                return $this->successData($data);
            }else{
                $data=DB::table('loghistories')
                ->select(DB::raw('count(*) as count,MONTH(logDate) as month'))
                ->where('userId',$id)
                ->orderBy('logDate','desc')
                ->groupBy(DB::raw('MONTH(logDate)'))
                ->get();
                return $this->successData($data);
            }
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }

    public function GetBrowserCount($id)
    {
        try
        {
            $objRole=Users::findOrFail($id);

            if($objRole->userRoleId==1){
                $data=DB::table('loghistories')
                ->select(DB::raw('count(*) as count,browser'))
                ->orderBy('logDate','desc')
                ->groupBy('browser')
                ->get();
                return $this->successData($data);
            }else{
                $data=DB::table('loghistories')
                ->select(DB::raw('count(*) as count,browser'))
                ->where('userId',$id)
                ->orderBy('logDate','desc')
                ->groupBy('browser')
                ->get();
                return $this->successData($data);
            }
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }

    public function GetPlatformCount($id)
    {
        try
        {
            $objRole=Users::findOrFail($id);

            if($objRole->userRoleId==1){
                $data=DB::table('loghistories')
                ->select(DB::raw('count(*) as count,platform'))
                ->orderBy('logDate','desc')
                ->groupBy('platform')
                ->get();
                return $this->successData($data);
            }else{
                $data=DB::table('loghistories')
                ->select(DB::raw('count(*) as count,platform'))
                ->where('userId',$id)
                ->orderBy('logDate','desc')
                ->groupBy('platform')
                ->get();
                return $this->successData($data);
            }
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }

    public function GetQuizCountByDate($id)
    {
        try
        {
            $objRole=Users::findOrFail($id);

            if($objRole->userRoleId==1){
                $data=DB::table('quizresponseinitials')
                ->select(DB::raw('count(*) as count,cast(dateAdded as date) date'))
                ->orderBy('dateAdded','desc')
                ->groupBy(DB::raw('cast(dateAdded as date)'))
                ->take(10)
                ->get();
                return $this->successData($data);
            }else{
                $data=DB::table('quizresponseinitials')
                ->select(DB::raw('count(*) as count,cast(dateAdded as date) date'))
                ->where('userId',$id)
                ->orderBy('dateAdded','desc')
                ->groupBy(DB::raw('cast(dateAdded as date)'))
                ->take(10)
                ->get();
                return $this->successData($data);
            }
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }

    public function GetUserCountByQuiz($id)
    {
        try
        {
            $objRole=Users::findOrFail($id);

            if($objRole->userRoleId==1){
                $data=DB::table('quizresponseinitials')
                ->select(DB::raw('count(*) as count,quizTitle'))
                ->orderBy('dateAdded','desc')
                ->groupBy('quizTitle')
                ->get();
                return $this->successData($data);
            }else{
                $data=DB::table('quizresponseinitials')
                ->select(DB::raw('count(*) as count,quizTitle'))
                ->where('userId',$id)
                ->orderBy('dateAdded','desc')
                ->groupBy('quizTitle')
                ->get();
                return $this->successData($data);
            }
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }



}
