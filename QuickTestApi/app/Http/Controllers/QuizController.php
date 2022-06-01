<?php

namespace App\Http\Controllers;

use App\Models\Quiz\CertificateTemplate;
use App\Models\Quiz\QuizMarkOption;
use App\Models\Quiz\QuizParticipant;
use App\Models\Quiz\QuizParticipantOption;
use App\Models\Quiz\QuizPayment;
use App\Models\Quiz\QuizResponseInitial;
use App\Models\Quiz\QuizResponseDetail;
use App\Models\Quiz\QuizTopic;
use App\Models\Quiz\ReportType;
use App\Models\Question\QuestionCategory;
use App\Models\Question\QuestionLavel;
use App\Models\Question\QuestionType;
use App\Models\Question\QuizQuestion;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class QuizController extends Controller
{
    use ApiResponser;

    public function GetQuizList()
    {
        try
        {
            $quizzes=DB::select('SELECT q.*,(select count(*) from quizquestions where quizTopicId=q.quizTopicId) as questionsCount FROM quiztopics q;');
            return $quizzes;
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }

    public function GetQuizesForReports()
    {
        try
        {
            $quizzes=QuizTopic::all();
            return $quizzes;
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }

    public function DeleteQuiz($id)
    {
        try
        {
            DB::beginTransaction();
            $quizObj=QuizTopic::where('quizTopicId',$id)->first();
            if($quizObj!=null){
                QuizTopic::where('quizTopicId',$id)->delete();
                QuizQuestion::where('quizTopicId',$id)->delete();
            }
            DB::commit();
            return $this->successMsg('Successfully deleted');
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }

    public function CreateQuizTopic(Request $request)
    {
        try
        {
            $request->validate([
                'quizTitle'=>'required|string',
                'quizTime'=>'required',
                'quizTotalMarks'=>'required',
                'quizPassMarks'=>'required',
                'quizMarkOptionId'=>'required',
                'quizParticipantOptionId'=>'required',
                'addedBy'=>'required'
            ]);

            $objCheck=QuizTopic::where(strtolower('quizTitle'),strtolower($request->quizTitle))->first();
            if($objCheck==null){
                QuizTopic::create([
                    'quizTitle'=>$request->quizTitle,
                    'quizTime'=>$request->quizTime,
                    'quizTotalMarks'=>$request->quizTotalMarks,
                    'quizPassMarks'=>$request->quizPassMarks,
                    'quizMarkOptionId'=>$request->quizMarkOptionId,
                    'quizParticipantOptionId'=>$request->quizParticipantOptionId,
                    'certificateTemplateId'=>$request->certificateTemplateId,
                    'allowMultipleInputByUser'=>$request->allowMultipleInputByUser,
                    'allowMultipleAnswer'=>$request->allowMultipleAnswer,
                    'allowMultipleAttempt'=>$request->allowMultipleAttempt,
                    'allowCorrectOption'=>$request->allowCorrectOption,
                    'quizscheduleStartTime'=>$request->quizscheduleStartTime,
                    'quizscheduleEndTime'=>$request->quizscheduleEndTime,
                    'quizPrice'=>$request->quizPrice,
                    'addedBy'=>$request->addedBy
                ]);
                return $this->successMsg('Successfully saved');

            }else{
                return $this->error('Duplicate Assessment Topic!',202,'error');
            }
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }

    public function UpdateQuizTopic(Request $request)
    {
        try
        {              
            $objQuizTopic=QuizTopic::where('quizTopicId',$request->quizTopicId)->first();
            $objCheck=QuizTopic::where(strtolower('quizTitle'),strtolower($request->quizTitle))->first();

            if($objCheck!=null and strtolower($objCheck->quizTitle)!=strtolower($objQuizTopic->quizTitle)){
                return $this->error('Duplicate Assessment Topic!',202,'error');
            }else{
                DB::beginTransaction();

                $message="Successfully updated";
                $previousMarkOptionId=$objQuizTopic->quizMarkOptionId;

                QuizTopic::where('quizTopicId',$request->quizTopicId)
                ->update([
                    'quizTitle'=>$request->quizTitle,
                    'quizTime'=>$request->quizTime,
                    'quizTotalMarks'=>$request->quizTotalMarks,
                    'quizPassMarks'=>$request->quizPassMarks,
                    'quizMarkOptionId'=>$request->quizMarkOptionId,
                    'quizParticipantOptionId'=>$request->quizParticipantOptionId,
                    'certificateTemplateId'=>$request->certificateTemplateId,
                    'allowMultipleInputByUser'=>$request->allowMultipleInputByUser,
                    'allowMultipleAnswer'=>$request->allowMultipleAnswer,
                    'allowMultipleAttempt'=>$request->allowMultipleAttempt,
                    'allowCorrectOption'=>$request->allowCorrectOption,
                    'quizscheduleStartTime'=>$request->quizscheduleStartTime,
                    'quizscheduleEndTime'=>$request->quizscheduleEndTime,
                    'quizPrice'=>$request->quizPrice,
                    'lastUpdatedBy'=>$request->lastUpdatedBy,
                    'lastUpdatedDate'=>now()
                ]);

                if($request->quizParticipantOptionId==1){
                    QuizParticipant::where('quizTopicId',$request->quizTopicId)->delete();
                }

                if(($previousMarkOptionId==1 || $previousMarkOptionId==3) && $request->quizMarkOptionId==2){
                    QuizTopic::where('quizTopicId',$request->quizTopicId)
                    ->update([
                        'quizTime'=>0,
                        'quizTotalMarks'=>0,
                        'quizPassMarks'=>0,
                        'allowMultipleAnswer'=>false 
                    ]);

                    QuizQuestion::where('quizTopicId',$request->quizTopicId)
                    ->update([
                        'correctOption'=>"",
                        'perQuestionMark'=>0
                    ]);

                    $message="As you Switched to Survey, Marks & Required time values are reset to initial state.";
                }

                if($previousMarkOptionId==2 && ($request->quizMarkOptionId==1 || $request->quizMarkOptionId==3)){
                    QuizTopic::where('quizTopicId',$request->quizTopicId)
                    ->update([
                        'isRunning'=>false,
                        'allowMultipleInputByUser'=>false 
                    ]);
                    if(QuizQuestion::where('quizTopicId',$request->quizTopicId)->count()>0){
                        $message="As you switched to marks based Assessment,you need to set correct answer for all questions of this Assessment.";
                    }
                }

                if($previousMarkOptionId==1 && $request->quizMarkOptionId==3){
                    QuizTopic::where('quizTopicId',$request->quizTopicId)
                    ->update([
                        'isRunning'=>false 
                    ]);
                    if(QuizQuestion::where('quizTopicId',$request->quizTopicId)->count()>0){
                        $message="As you switched to Question wise set Mark Option,you need to check correct answer & marks for all questions of this Assessment.";
                    }
                }

                if($previousMarkOptionId==3 && $request->quizMarkOptionId==1){
                    QuizTopic::where('quizTopicId',$request->quizTopicId)
                    ->update([
                        'isRunning'=>false 
                    ]);
                    if(QuizQuestion::where('quizTopicId',$request->quizTopicId)->count()>0){
                        $message="As you switched to Equal distribution Mark Option,you have to live this Assessment again.";
                    }
                }

                DB::commit();
                return $this->successMsg($message);
            }
            
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }

    public function StartQuiz(Request $request)
    {
        try
        {
            $questionsCount=QuizQuestion::where('quizTopicId',$request->quizTopicId)->count();
            $questionsMcq=QuizQuestion::where('quizTopicId',$request->quizTopicId)
            ->where('questionTypeId',1)
            ->get();
            $questionsAll=QuizQuestion::where('quizTopicId',$request->quizTopicId)
            ->get();

            $isCorrectOptionEmpty=false;

            foreach($questionsMcq as $item){
                if($item->correctOption==""){
                    $isCorrectOptionEmpty=true;
                }
            }

            $isMarksEmpty=false;
            foreach($questionsAll as $item){
                if($item->perQuestionMark<=0){
                    $isMarksEmpty=true;
                }
            }

            if($questionsCount==0){
                return $this->error('Not possible to live this Assessment.Please add some questions!',202,'error');
            }else if($isCorrectOptionEmpty==true && ($request->quizMarkOptionId==1 || $request->quizMarkOptionId==3)){
                return $this->error('Not possible to live this Assessment.Questions have no correct answer.Please set them first!',202,'error');
            }else if($isMarksEmpty==true && $request->quizMarkOptionId==3){
                return $this->error('Not possible to live this Assessment.Questions marks not set yet.Please set them first!',202,'error');
            }else{
                DB::beginTransaction();

                QuizTopic::where('quizTopicId',$request->quizTopicId)
                ->update([
                    'isRunning'=>true 
                ]);

                if($request->quizMarkOptionId==1){
                    $perQuestionMark=$request->quizTotalMarks/$questionsCount;
                    $questionsToUpdateMarks=QuizQuestion::where('quizTopicId',$request->quizTopicId)->get();

                    foreach($questionsToUpdateMarks as $item){
                        QuizQuestion::where('quizQuestionId',$item->quizQuestionId)
                        ->update([
                            'perQuestionMark'=>$perQuestionMark
                        ]);
                    }
                }else if($request->quizMarkOptionId==2){
                    QuizTopic::where('quizTopicId',$request->quizTopicId)
                    ->update([
                        'allowMultipleAnswer'=>false,
                        'quizTotalMarks'=>0,
                        'quizTime'=>0
                    ]);

                    $listOfQuestions=QuizQuestion::where('quizTopicId',$request->quizTopicId)->get();

                    foreach($listOfQuestions as $item){
                        QuizQuestion::where('quizQuestionId',$item->quizQuestionId)
                        ->update([
                            'correctOption'=>'',
                            'perQuestionMark'=>0
                        ]);
                    }
                }else if($request->quizMarkOptionId==3){
                    $totalMarks=DB::table('quizquestions')->where('quizTopicId',$request->quizTopicId)->sum('perQuestionMark');
                    QuizTopic::where('quizTopicId',$request->quizTopicId)
                    ->update([
                        'quizTotalMarks'=>$totalMarks
                    ]);
                }

                DB::commit();
                return $this->successMsg('This Assessment is live now!');
            }
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }

    public function StopQuiz(Request $request)
    {
        try
        {
            QuizTopic::where('quizTopicId',$request->quizTopicId)
            ->update([
                'isRunning'=>false
            ]);
            return $this->successMsg('This Assessment is stopped from live!');
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }

    public function GetQuizParticipantOptionList()
    {
        try
        {
            $data=QuizParticipantOption::all();
            return $data;
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }

    public function GetQuizMarksOptionList()
    {
        try
        {
            $data=QuizMarkOption::all();
            return $data;
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }

    public function CreateQuizParticipants(Request $request)
    {
        try
        {          
            DB::beginTransaction();

            $emails=$request->all();
            $emailsToDelete=QuizParticipant::where('quizTopicId',$request[0]['quizTopicId'])->get();
            foreach($emailsToDelete as $item){
                QuizParticipant::where('quizParticipantId',$item->quizParticipantId)->delete();
            }
            
            for($i=0;$i<count($emails);$i++){
                QuizParticipant::create([
                    'email'=>$request[$i]['email'],
                    'quizTopicId'=>$request[$i]['quizTopicId']
                ]);
            }   

            DB::commit();
            return $this->successMsg('Successfully saved');
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }

    public function GetQuizParticipantsEmail($quizTopicId)
    {
        try
        {
            $emailList=QuizParticipant::where('quizTopicId',$quizTopicId)->get();
            $emails='';
            foreach($emailList as $item){
                $emails.=$item->email.',';
            }
            return rtrim($emails,',');
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }

    public function GetQuizWithQuestionCount()
    {
        try
        {
            $quizzes=DB::select('SELECT q.*,(select count(*) from quizquestions where quizTopicId=q.quizTopicId) as questionsCount FROM quiztopics q;');
            return $quizzes;
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }

    public function GetQuizQuestionList($id)
    {
        try
        {
            $questions=DB::table('quizquestions')
            ->join('questioncategories','quizquestions.questionCategoryId','=','questioncategories.questionCategoryId')
            ->select('quizquestions.*','questioncategories.questionCategoryName')
            ->where('quizquestions.quizTopicId','=',$id)
            ->get();
            return $questions;
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }

    public function GetSingleQuestion($quizId,$serial)
    {
        try
        {
            $singleQuestion=DB::table('quizquestions')
            ->join('questioncategories','quizquestions.questionCategoryId','=','questioncategories.questionCategoryId')
            ->join('questionlavels','quizquestions.questionLavelId','=','questionlavels.questionLavelId')
            ->select('quizquestions.*','questioncategories.questionCategoryName','questionlavels.questionLavelName')
            ->where('quizquestions.quizTopicId','=',$quizId)
            ->where('quizquestions.serialNo','=',$serial)
            ->first();
            return $singleQuestion;
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }

    public function DeleteSingleQuizQuestion($id)
    {
        try
        {
            DB::beginTransaction();

            $msg='Successfully deleted';
            $objQuizQuestion=QuizQuestion::where('quizQuestionId',$id)->first();
            QuizQuestion::where('quizQuestionId',$id)->delete(); 
                     
            if(QuizQuestion::where('quizTopicId',$objQuizQuestion->quizTopicId)->count()>0){           
                $allQuestions=QuizQuestion::where('quizTopicId',$objQuizQuestion->quizTopicId)->get();
                $counter=1;
                foreach($allQuestions as $item){
                    QuizQuestion::where('quizQuestionId',$item->quizQuestionId)
                    ->update([
                        'serialNo'=>$counter
                    ]);
                    $counter++;
                }

                $objCheckQuizRunning=QuizTopic::where('quizTopicId',$objQuizQuestion->quizTopicId)->first();
                if($objCheckQuizRunning->isRunning==true){
                    QuizTopic::where('quizTopicId',$objQuizQuestion->quizTopicId)
                    ->update([
                        'isRunning'=>false
                    ]);
                    $msg="As you deleted a question from a live Assessment, you have to start this Assessment again from Assessments List.";
                }
            }
            DB::commit();
            return $this->successMsg($msg);
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }

    public function CreateQuizQuestion(Request $request)
    {
        try
        {
            $objCheck=QuizQuestion::where('quizTopicId',$request->quizTopicId)
            ->where(strtolower('questionDetail'),strtolower($request->questionDetail))
            ->first();

            if($objCheck==null){
                DB::beginTransaction();

                QuizQuestion::create([
                    'quizTopicId'=>$request->quizTopicId,
                    'questionDetail'=>$request->questionDetail,
                    'isCodeSnippet'=>$request->isCodeSnippet==null?0:$request->isCodeSnippet,
                    'serialNo'=>QuizQuestion::where('quizTopicId',$request->quizTopicId)->count()+1,
                    'perQuestionMark'=>$request->perQuestionMark==null?0:$request->perQuestionMark,
                    'questionTypeId'=>$request->questionTypeId,
                    'questionLavelId'=>$request->questionLavelId,
                    'questionCategoryId'=>$request->questionCategoryId,
                    'optionA'=>$request->optionA,
                    'optionB'=>$request->optionB,
                    'optionC'=>$request->optionC,
                    'optionD'=>$request->optionD,
                    'optionE'=>$request->optionE,
                    'correctOption'=>$request->correctOption,
                    'answerExplanation'=>$request->answerExplanation,
                    'imagePath'=>$request->imagePath,
                    'videoPath'=>$request->videoPath,
                    'addedBy'=>$request->addedBy
                ]);

                $uniqueCategories=DB::table('quizquestions')
                ->select('quizTopicId','questionCategoryId')
                ->where('quizTopicId','=',$request->quizTopicId)
                ->groupBy('quizTopicId','questionCategoryId')
                ->get();
                $categories="";
                foreach($uniqueCategories as $item){
                    $categories.=$item->questionCategoryId.',';
                }
                QuizTopic::where('quizTopicId',$request->quizTopicId)
                ->update([
                    'categories'=>rtrim($categories,',')
                ]);

                $msg="";
                $objCheckQuiz=QuizTopic::where('quizTopicId',$request->quizTopicId)->first();

                if($objCheckQuiz->isRunning==true && ($objCheckQuiz->quizMarkOptionId==1 || $objCheckQuiz->quizMarkOptionId==3)){
                    QuizTopic::where('quizTopicId',$request->quizTopicId)
                    ->update([
                        'isRunning'=>false
                    ]);
                    $msg="As you added a new question to a live Assessment, you have to start this Assessment again from Assessments list";
                }else{
                    $msg="Successfully saved";
                }

                DB::commit();
                return $this->successMsg($msg);
            }else{
                return $this->error('Duplicate question!',202,'error');
            }
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }

    public function UpdateQuizQuestion(Request $request)
    {
        try
        {
            $objQuizQuestion=QuizQuestion::where('quizQuestionId',$request->quizQuestionId)->first();
            $objCheck=QuizQuestion::where('quizTopicId',$request->quizTopicId)
            ->where(strtolower('questionDetail'),strtolower($request->questionDetail))
            ->first();

            if($objCheck!=null && $objCheck->quizTopicId==$objQuizQuestion->quizTopicId && strtolower($objCheck->questionDetail)!=strtolower($objQuizQuestion->questionDetail)){
                return $this->error('Duplicate question!',202,'error');
            }else{
                $message="Successfully updated";

                DB::beginTransaction();

                $objCheckQuiz=QuizTopic::where('quizTopicId',$request->quizTopicId)->first();

                if($objCheckQuiz->quizMarkOptionId==3){
                    QuizQuestion::where('quizQuestionId',$request->quizQuestionId)
                    ->update([
                        'perQuestionMark'=>$request->perQuestionMark
                    ]);

                    if($objCheckQuiz->isRunning==true && $objQuizQuestion->perQuestionMark !=$request->perQuestionMark){
                        QuizTopic::where('quizTopicId',$request->quizTopicId)
                        ->update([
                            'isRunning'=>false
                        ]);

                        $message="This item is stopped from live as you changed marks of a running Assessment. you have to start this Assessment again from Assessments list ";
                    }
                }

                QuizQuestion::where('quizQuestionId',$request->quizQuestionId)
                ->update([
                    'quizTopicId'=>$request->quizTopicId,
                    'questionDetail'=>$request->questionDetail,
                    'isCodeSnippet'=>$request->isCodeSnippet==null?0:$request->isCodeSnippet,
                    'questionTypeId'=>$request->questionTypeId,
                    'questionLavelId'=>$request->questionLavelId,
                    'questionCategoryId'=>$request->questionCategoryId,
                    'optionA'=>$request->optionA,
                    'optionB'=>$request->optionB,
                    'optionC'=>$request->optionC,
                    'optionD'=>$request->optionD,
                    'optionE'=>$request->optionE,
                    'correctOption'=>$request->correctOption,
                    'answerExplanation'=>$request->answerExplanation,
                    'imagePath'=>$request->imagePath,
                    'videoPath'=>$request->videoPath,
                    'lastUpdatedBy'=>$request->lastUpdatedBy,
                    'lastUpdatedDate'=>now()
                ]);

                $uniqueCategories=DB::table('quizquestions')
                ->select('quizTopicId','questionCategoryId')
                ->where('quizTopicId','=',$request->quizTopicId)
                ->groupBy('quizTopicId','questionCategoryId')
                ->get();
                $categories="";
                foreach($uniqueCategories as $item){
                    $categories.=$item->questionCategoryId.',';
                }
                QuizTopic::where('quizTopicId',$request->quizTopicId)
                ->update([
                    'categories'=>rtrim($categories,',')
                ]);

                DB::commit();
                return $this->successMsg($message);
            }

        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }

    public function UploadQuestionImage(Request $request)
    {
        try
        {
            $this->validate($request, [
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,bmp,tif,tiff',
            ]);
    
            if(!$request->hasFile('image')) {
                return $this->error('upload file not found',202,'error');
            }
            $file = $request->file('image');
            if(!$file->isValid()) {
                return $this->error('invalid file upload',202,'error');
            }
            $path=public_path().'/Upload/QuestionImages';
            $fileName=time().$file->getClientOriginalName();
            $file->move($path, $fileName);
            $pathReturn='\\Upload\\QuestionImages\\'.$fileName;
            return $this->successData($pathReturn);
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }

    public function CreateQuizResponseInitial(Request $request)
    {
        try
        {
            $attemptCount=QuizResponseInitial::where('userId',$request->userId)
            ->where('quizTopicId',$request->quizTopicId)
            ->count();

            QuizResponseInitial::create([
                'userId'=>$request->userId,
                'email'=>$request->email,
                'attemptCount'=>$attemptCount+1,
                'isExamined'=>true,
                'quizTopicId'=>$request->quizTopicId,
                'quizTitle'=>$request->quizTitle,
                'quizMark'=>$request->quizMark,
                'userObtainedQuizMark'=>$request->userObtainedQuizMark,
                'quizTime'=>$request->quizTime,
                'timeTaken'=>$request->timeTaken,
                'startTime'=>now(),
                'addedBy'=>$request->addedBy
            ]);
            return QuizResponseInitial::where('userId',$request->userId)->orderByDesc('dateadded')->first();
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }

    public function CreateQuizResponseDetail(Request $request)
    {
        try
        {
            DB::beginTransaction();

            $questionInfo=QuizQuestion::where('quizQuestionId',$request->quizQuestionId)->first();
            if($request->isAnswerSkipped==false && $questionInfo->questionTypeId==1 && $request->correctAnswer!=''){
                if(str_contains($request->correctAnswer,$request->userAnswer)){
                    $request->userObtainedQuestionMark=$request->questionMark;
                }else{
                    $request->userObtainedQuestionMark=0;
                }
                $request->isExamined=true;
            }else if($request->isAnswerSkipped==false && $questionInfo->questionTypeId==2){
                $request->userObtainedQuestionMark=0;
                $request->isExamined=false;
            }else{
                $request->userObtainedQuestionMark=0;
                $request->isExamined=true;
            }

            QuizResponseDetail::create([
                'quizResponseInitialId'=>$request->quizResponseInitialId,
                'quizQuestionId'=>$request->quizQuestionId,
                'questionDetail'=>$questionInfo->questionDetail,
                'userAnswer'=>$request->userAnswer,
                'isAnswerSkipped'=>$request->isAnswerSkipped,
                'correctAnswer'=>$questionInfo->correctOption,
                'answerExplanation'=>$questionInfo->answerExplanation,
                'questionMark'=>$questionInfo->perQuestionMark,
                'userObtainedQuestionMark'=>$request->userObtainedQuestionMark,
                'imagePath'=>$questionInfo->imagePath,
                'videoPath'=>$questionInfo->videoPath,
                'isExamined'=>$request->isExamined,
                'addedBy'=>$request->addedBy
            ]);

            $sumOfQuizMark=0;
            $objInitial=QuizResponseInitial::where('quizResponseInitialId',$request->quizResponseInitialId)->first();
            $diffTwoDateTime=now()->diff($objInitial->startTime);
            $timeTakenForQuiz=$diffTwoDateTime->format('%H hour %I minute %S second');
            $objQuizTopic=QuizTopic::where('quizTopicId',$questionInfo->quizTopicId)->first();

            if($questionInfo->questionTypeId==2){
                if($request->isAnswerSkipped==false && $objQuizTopic->quizMarkOptionId!=2){
                    $objInitial->isExamined=false;
                }
            }else if($questionInfo->questionTypeId==1){
                if($objQuizTopic->quizMarkOptionId==1 || $objQuizTopic->quizMarkOptionId==3){
                    $sumOfQuizMark=DB::table('quizresponsedetails')->where('quizResponseInitialId',$request->quizResponseInitialId)->sum('userObtainedQuestionMark');
                    $objInitial->userObtainedQuizMark=$sumOfQuizMark;
                }
            }

            QuizResponseInitial::where('quizResponseInitialId',$request->quizResponseInitialId)
            ->update([
                'endTime'=>now(),
                'timeTaken'=>$timeTakenForQuiz,
                'isExamined'=>$objInitial->isExamined,
                'userObtainedQuizMark'=>$objInitial->userObtainedQuizMark
            ]);

            DB::commit();

            return $this->successMsg('Successfully saved');
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }

    public function UpdateQuizTakenTime(Request $request)
    {
        try
        {
            $objInitial=QuizResponseInitial::where('quizResponseInitialId',$request->quizResponseInitialId)->first();
            $diffTwoDateTime=now()->diff($objInitial->startTime);
            $timeTakenForQuiz=$diffTwoDateTime->format('%H hour %I minute %S second');
            QuizResponseInitial::where('quizResponseInitialId',$request->quizResponseInitialId)
            ->update([
                'endTime'=>now(),
                'timeTaken'=>$timeTakenForQuiz
            ]);
            return $this->successMsg('Successfully updated');
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }

    public function UploadQuestionCsv(Request $request)
    {
        try
        {
            $this->validate($request, [
                'csv' => 'required|mimes:csv,txt',
            ]);
    
            if(!$request->hasFile('csv')) {
                return $this->error('upload file not found',202,'error');
            }
            $file = $request->file('csv');
            if(!$file->isValid()) {
                return $this->error('invalid file upload',202,'error');
            }
            $path=public_path().'/Upload/QuestionCsv';
            $fileName=time().$file->getClientOriginalName();
            $file->move($path, $fileName);
            $pathReturn='\\Upload\\QuestionCsv\\'.$fileName;
            return $this->successData($pathReturn);
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }

    public function ReadQuestionUploadCsv(Request $request)
    {
        try
        {
            $csvPath=public_path().$request->path;
            $uploadData=$this->csvToArray($csvPath);
            $data=[];

            DB::beginTransaction();

            for ($i = 0; $i < count($uploadData); $i ++)
            {
                $data[]=[
                    'quizTopicId'=>$request->quizTopicId,
                    'questionDetail'=>$uploadData[$i]['questionDetail'],
                    'perQuestionMark'=>$uploadData[$i]['perQuestionMark'],
                    'questionTypeId'=>$uploadData[$i]['questionTypeId'],
                    'questionLavelId'=>$uploadData[$i]['questionLavelId'],
                    'questionCategoryId'=>$uploadData[$i]['questionCategoryId'],
                    'optionA'=>$uploadData[$i]['optionA'],
                    'optionB'=>$uploadData[$i]['optionB'],
                    'optionC'=>$uploadData[$i]['optionC'],
                    'optionD'=>$uploadData[$i]['optionD'],
                    'optionE'=>$uploadData[$i]['optionE'],
                    'correctOption'=>$uploadData[$i]['correctOption'],
                    'answerExplanation'=>$uploadData[$i]['answerExplanation'],
                    'addedBy'=>$request->addedBy
                ];
            }

            DB::table('quizquestions')->insert($data);

            $allQuestions=QuizQuestion::where('quizTopicId',$request->quizTopicId)->get();
            $counter=1;
            foreach($allQuestions as $item){
                QuizQuestion::where('quizQuestionId',$item->quizQuestionId)
                ->update([
                    'serialNo'=>$counter
                ]);
                $counter++;
            }

            $uniqueCategories=DB::table('quizquestions')
            ->select('quizTopicId','questionCategoryId')
            ->where('quizTopicId','=',$request->quizTopicId)
            ->groupBy('quizTopicId','questionCategoryId')
            ->get();
            $categories="";
            foreach($uniqueCategories as $item){
                $categories.=$item->questionCategoryId.',';
            }
            QuizTopic::where('quizTopicId',$request->quizTopicId)
            ->update([
                'categories'=>rtrim($categories,','),
                'isRunning'=>false
            ]);

            DB::commit();

            return $this->successMsg('As you added bulk questions, you have to start this Assessment again from Assessments list if it was live');           
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }

    public function GetCertificateTemplates()
    {
        try
        {
            $data=CertificateTemplate::all();
            return $data;
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }

    public function UploadCertificateImage(Request $request)
    {
        try
        {
            $this->validate($request, [
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,bmp,tif,tiff',
            ]);
    
            if(!$request->hasFile('image')) {
                return $this->error('upload file not found',202,'error');
            }
            $file = $request->file('image');
            if(!$file->isValid()) {
                return $this->error('invalid file upload',202,'error');
            }
            $path=public_path().'/Upload/Certificate';
            $fileName=time().$file->getClientOriginalName();
            $file->move($path, $fileName);
            $pathReturn='\\Upload\\Certificate\\'.$fileName;
            return $this->successData($pathReturn);
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }

    public function CreateCertificateTemplate(Request $request)
    {
        try
        {
            $request->validate([
                'title'=>'required|string',
                'heading'=>'required|string',
                'mainText'=>'required|string'
            ]);

            CertificateTemplate::create([
                'title'=>$request->title,
                'heading'=>$request->heading,
                'mainText'=>$request->mainText,
                'publishDate'=>$request->publishDate,
                'topLeftImagePath'=>$request->topLeftImagePath,
                'topRightImagePath'=>$request->topRightImagePath,
                'bottomMiddleImagePath'=>$request->bottomMiddleImagePath,
                'backgroundImagePath'=>$request->backgroundImagePath,
                'leftSignatureText'=>$request->leftSignatureText,
                'leftSignatureImagePath'=>$request->leftSignatureImagePath,
                'rightSignatureText'=>$request->rightSignatureText,
                'rightSignatureImagePath'=>$request->rightSignatureImagePath,
                'addedBy'=>$request->addedBy
            ]);
            return $this->successMsg('Successfully saved');
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }

    public function UpdateCertificateTemplate(Request $request)
    {
        try
        {
            $request->validate([
                'certificateTemplateId'=>'required',
                'title'=>'required|string',
                'heading'=>'required|string',
                'mainText'=>'required|string'
            ]);

            CertificateTemplate::where('certificateTemplateId',$request->certificateTemplateId)
            ->update([
                'title'=>$request->title,
                'heading'=>$request->heading,
                'mainText'=>$request->mainText,
                'publishDate'=>$request->publishDate,
                'topLeftImagePath'=>$request->topLeftImagePath,
                'topRightImagePath'=>$request->topRightImagePath,
                'bottomMiddleImagePath'=>$request->bottomMiddleImagePath,
                'backgroundImagePath'=>$request->backgroundImagePath,
                'leftSignatureText'=>$request->leftSignatureText,
                'leftSignatureImagePath'=>$request->leftSignatureImagePath,
                'rightSignatureText'=>$request->rightSignatureText,
                'rightSignatureImagePath'=>$request->rightSignatureImagePath,
                'lastUpdatedBy'=>$request->lastUpdatedBy,
                'lastUpdatedDate'=>now()
            ]);
            return $this->successMsg('Successfully updated');
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }

    public function DeleteSingleTemplate($id)
    {
        try
        {
            CertificateTemplate::where('certificateTemplateId',$id)->delete();
            return $this->successMsg('Successfully deleted');
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }

    public function GetSingleTemplate($id)
    {
        try
        {
            $data=CertificateTemplate::where('certificateTemplateId',$id)->first();
            return $data;
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }

    public function GetQuestionType()
    {
        try
        {
            $data=QuestionType::all();
            return $data;
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }

    public function GetQuestionLavel()
    {
        try
        {
            $data=QuestionLavel::all();
            return $data;
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }

    public function GetQuestionCategory()
    {
        try
        {
            $data=QuestionCategory::all();
            return $data;
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }

    public function DeleteSingleQuestionCategory($id)
    {
        try
        {
            if(QuizQuestion::where('questionCategoryId',$id)->count()==0){
                QuestionCategory::where('questionCategoryId',$id)->delete();
                return $this->successMsg('Successfully deleted');
            }else{
                return $this->error('This category is using with question. Not possible to delete.',202,'error');
            }
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }

    public function CreateQuestionCategory(Request $request)
    {
        try
        {
            $request->validate([
                'questionCategoryName'=>'required|string',
                'addedBy'=>'required'
            ]);

            if(QuestionCategory::where(strtolower('questionCategoryName'),strtolower($request->questionCategoryName))->first()==null){
                QuestionCategory::create([
                    'questionCategoryName'=>$request->questionCategoryName,
                    'addedBy'=>$request->addedBy
                ]);
                return $this->successMsg('Successfully saved');
            }else{
                return $this->error($ex->getMessage(),202,'error');
            }
        }
        catch(\Exception $ex)
        {
            return $this->error('This Category already exists!',202,'error');
        }
    }

    public function UpdateQuestionCategory(Request $request)
    {
        try
        {
            $request->validate([
                'questionCategoryId'=>'required',
                'questionCategoryName'=>'required|string',
                'lastUpdatedBy'=>'required'
            ]);

            $objCategory=QuestionCategory::where('questionCategoryId',$request->questionCategoryId)->first();
            $objCheck=QuestionCategory::where(strtolower('questionCategoryName'),strtolower($request->questionCategoryName))->first();

            if($objCheck!=null && strtolower($objCheck->questionCategoryName)!=strtolower($objCategory->questionCategoryName)){
                return $this->error('This Category already exists!',202,'duplicate');
            }else{
                QuestionCategory::where('questionCategoryId',$request->questionCategoryId)
                ->update([
                    'questionCategoryName'=>$request->questionCategoryName,
                    'lastUpdatedBy'=>$request->addedBy,
                    'lastUpdatedDate'=>now()
                ]);
                return $this->successMsg('Successfully saved');
            }
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }

    public function CreateQuizPayment(Request $request)
    {
        try
        {
            $request->validate([
                'quizTopicId'=>'required',
                'email'=>'required|string',
                'currency'=>'required|string',
                'addedBy'=>'required'
            ]);

            QuizPayment::create([
                'quizTopicId'=>$request->quizTopicId,
                'email'=>$request->email,
                'currency'=>$request->currency,
                'addedBy'=>$request->addedBy
            ]);
            return $this->successMsg('Successfully saved');
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }

}
