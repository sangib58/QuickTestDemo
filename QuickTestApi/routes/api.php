<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\PaymentsController;
use App\Http\Controllers\ReportController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['json.response'])->group(function(){
    Route::post('User/GetLoginInfo', [UserController::class, 'GetLoginInfo']);
    Route::get('User/GetUserInfoForForgetPassword/{email}', [UserController::class, 'GetUserInfoForForgetPassword']);
    Route::put('User/ResetUserPassword', [UserController::class, 'ResetUserPassword']);
    Route::post('User/StudentRegistration', [UserController::class, 'StudentRegistration']);
    Route::get('User/UpdateLoginHistory/{logCode}', [UserController::class, 'UpdateLoginHistory']);
    Route::post('User/CreateLoginHistory', [UserController::class, 'CreateLoginHistory']);

    Route::get('Settings/GetSiteSettings', [SettingsController::class, 'GetSiteSettings']);
    Route::put('Settings/UpdateClientUrl', [SettingsController::class, 'UpdateClientUrl']);
    Route::post('Settings/SendWelcomeMail', [SettingsController::class, 'SendWelcomeMail']);
    Route::post('Settings/SendPasswordResetLink', [SettingsController::class, 'SendPasswordResetLink']);
});

Route::middleware(['auth:sanctum','json.response'])->group(function(){
    //User Controller   
    Route::get('User/GetBrowseList', [UserController::class, 'GetBrowseList'])->middleware('permission.admin');
    Route::get('User/GetUserList', [UserController::class, 'GetUserList'])->middleware('permission.admin');
    Route::get('User/GetSingleUser/{id}', [UserController::class, 'GetSingleUser'])->middleware('permission.common');
    Route::delete('User/DeleteSingleUser/{id}', [UserController::class, 'DeleteSingleUser'])->middleware('permission.admin');
    Route::post('User/CreateUser', [UserController::class, 'CreateUser'])->middleware('permission.admin');
    Route::put('User/UpdateUser', [UserController::class, 'UpdateUser'])->middleware('permission.admin');
    Route::put('User/UpdateUserProfile', [UserController::class, 'UpdateUserProfile'])->middleware('permission.common');
    Route::post('User/Unlock', [UserController::class, 'Unlock'])->middleware('permission.common');
    Route::put('User/ChangeUserPassword', [UserController::class, 'ChangeUserPassword'])->middleware('permission.common');
    Route::post('User/Upload', [UserController::class, 'Upload'])->middleware('permission.common');
    Route::get('User/GetUserRoleList', [UserController::class, 'GetUserRoleList'])->middleware('permission.admin');
    Route::delete('User/DeleteSingleRole/{id}', [UserController::class, 'DeleteSingleRole'])->middleware('permission.admin');
    Route::post('User/CreateUserRole', [UserController::class, 'CreateUserRole'])->middleware('permission.admin');
    Route::put('User/UpdateUserRole', [UserController::class, 'UpdateUserRole'])->middleware('permission.admin');

    //Menu Controller
    Route::get('Menu/GetMenuList', [MenuController::class, 'GetMenuList'])->middleware('permission.admin');
    Route::delete('Menu/DeleteSingleMenu/{id}', [MenuController::class, 'DeleteSingleMenu'])->middleware('permission.admin');
    Route::post('Menu/CreateMenu', [MenuController::class, 'CreateMenu'])->middleware('permission.admin');
    Route::put('Menu/UpdateMenu', [MenuController::class, 'UpdateMenu'])->middleware('permission.admin');
    Route::get('Menu/GetSidebarMenu/{roleId}', [MenuController::class, 'GetSidebarMenu'])->middleware('permission.common');
    Route::get('Menu/GetAllMenu/{userRoleId}', [MenuController::class, 'GetAllMenu'])->middleware('permission.admin');
    Route::post('Menu/MenuAssign', [MenuController::class, 'MenuAssign'])->middleware('permission.admin');

    //Dashboard Controller
    Route::get('Dashboard/GetRunningQuizes/{email}', [DashboardController::class, 'GetRunningQuizes'])->middleware('permission.common');
    Route::get('Dashboard/GetStatus', [DashboardController::class, 'GetStatus'])->middleware('permission.admin');
    Route::get('Dashboard/GetLogInSummaryByDate/{id}', [DashboardController::class, 'GetLogInSummaryByDate'])->middleware('permission.common');
    Route::get('Dashboard/GetLogInSummaryByMonth/{id}', [DashboardController::class, 'GetLogInSummaryByMonth'])->middleware('permission.common');
    Route::get('Dashboard/GetBrowserCount/{id}', [DashboardController::class, 'GetBrowserCount'])->middleware('permission.common');
    Route::get('Dashboard/GetPlatformCount/{id}', [DashboardController::class, 'GetPlatformCount'])->middleware('permission.common');
    Route::get('Dashboard/GetQuizCountByDate/{id}', [DashboardController::class, 'GetQuizCountByDate'])->middleware('permission.common');
    Route::get('Dashboard/GetUserCountByQuiz/{id}', [DashboardController::class, 'GetUserCountByQuiz'])->middleware('permission.common');

    //Quiz Controller
    Route::get('Quiz/GetQuizList', [QuizController::class, 'GetQuizList'])->middleware('permission.common');
    Route::get('Quiz/GetQuizesForReports', [QuizController::class, 'GetQuizesForReports'])->middleware('permission.common');
    Route::delete('Quiz/DeleteQuiz/{id}', [QuizController::class, 'DeleteQuiz'])->middleware('permission.common');
    Route::post('Quiz/CreateQuizTopic', [QuizController::class, 'CreateQuizTopic'])->middleware('permission.common');
    Route::put('Quiz/UpdateQuizTopic', [QuizController::class, 'UpdateQuizTopic'])->middleware('permission.common');
    Route::put('Quiz/StartQuiz', [QuizController::class, 'StartQuiz'])->middleware('permission.common');
    Route::put('Quiz/StopQuiz', [QuizController::class, 'StopQuiz'])->middleware('permission.common');
    Route::get('Quiz/GetQuizParticipantOptionList', [QuizController::class, 'GetQuizParticipantOptionList'])->middleware('permission.common');
    Route::get('Quiz/GetQuizMarksOptionList', [QuizController::class, 'GetQuizMarksOptionList'])->middleware('permission.common');
    Route::post('Quiz/CreateQuizParticipants', [QuizController::class, 'CreateQuizParticipants'])->middleware('permission.common');
    Route::get('Quiz/GetQuizParticipantsEmail/{quizTopicId}', [QuizController::class, 'GetQuizParticipantsEmail'])->middleware('permission.common');
    Route::get('Quiz/GetQuizWithQuestionCount', [QuizController::class, 'GetQuizWithQuestionCount'])->middleware('permission.common');
    Route::get('Quiz/GetQuizQuestionList/{id}', [QuizController::class, 'GetQuizQuestionList'])->middleware('permission.common');
    Route::get('Quiz/GetSingleQuestion/{quizId}/{serial}', [QuizController::class, 'GetSingleQuestion'])->middleware('permission.common');
    Route::delete('Quiz/DeleteSingleQuizQuestion/{id}', [QuizController::class, 'DeleteSingleQuizQuestion'])->middleware('permission.common');
    Route::post('Quiz/CreateQuizQuestion', [QuizController::class, 'CreateQuizQuestion'])->middleware('permission.common');
    Route::put('Quiz/UpdateQuizQuestion', [QuizController::class, 'UpdateQuizQuestion'])->middleware('permission.common');
    Route::post('Quiz/UploadQuestionImage', [QuizController::class, 'UploadQuestionImage'])->middleware('permission.common');
    Route::post('Quiz/CreateQuizResponseInitial', [QuizController::class, 'CreateQuizResponseInitial'])->middleware('permission.common');
    Route::post('Quiz/CreateQuizResponseDetail', [QuizController::class, 'CreateQuizResponseDetail'])->middleware('permission.common');
    Route::put('Quiz/UpdateQuizTakenTime', [QuizController::class, 'UpdateQuizTakenTime'])->middleware('permission.common');
    Route::post('Quiz/UploadQuestionCsv', [QuizController::class, 'UploadQuestionCsv'])->middleware('permission.common');
    Route::post('Quiz/ReadQuestionUploadCsv', [QuizController::class, 'ReadQuestionUploadCsv'])->middleware('permission.common');
    Route::get('Quiz/GetCertificateTemplates', [QuizController::class, 'GetCertificateTemplates'])->middleware('permission.common');
    Route::post('Quiz/UploadCertificateImage', [QuizController::class, 'UploadCertificateImage'])->middleware('permission.common');
    Route::post('Quiz/CreateCertificateTemplate', [QuizController::class, 'CreateCertificateTemplate'])->middleware('permission.common');
    Route::put('Quiz/UpdateCertificateTemplate', [QuizController::class, 'UpdateCertificateTemplate'])->middleware('permission.common');
    Route::delete('Quiz/DeleteSingleTemplate/{id}', [QuizController::class, 'DeleteSingleTemplate'])->middleware('permission.common');
    Route::get('Quiz/GetSingleTemplate/{id}', [QuizController::class, 'GetSingleTemplate'])->middleware('permission.common');
    Route::get('Quiz/GetQuestionType', [QuizController::class, 'GetQuestionType'])->middleware('permission.common');
    Route::get('Quiz/GetQuestionLavel', [QuizController::class, 'GetQuestionLavel'])->middleware('permission.common');
    Route::get('Quiz/GetQuestionCategory', [QuizController::class, 'GetQuestionCategory'])->middleware('permission.common');
    Route::delete('Quiz/DeleteSingleQuestionCategory/{id}', [QuizController::class, 'DeleteSingleQuestionCategory'])->middleware('permission.common');
    Route::post('Quiz/CreateQuestionCategory', [QuizController::class, 'CreateQuestionCategory'])->middleware('permission.common');
    Route::put('Quiz/UpdateQuestionCategory', [QuizController::class, 'UpdateQuestionCategory'])->middleware('permission.common');
    Route::post('Quiz/CreateQuizPayment', [QuizController::class, 'CreateQuizPayment'])->middleware('permission.common');

    //Report Controller
    Route::get('Report/GetFinishedExamInfo/{id}', [ReportController::class, 'GetFinishedExamInfo'])->middleware('permission.common');
    Route::get('Report/GetFinishedExamResult/{id}', [ReportController::class, 'GetFinishedExamResult'])->middleware('permission.common');
    Route::get('Report/GetPendingExamine/{id}', [ReportController::class, 'GetPendingExamine'])->middleware('permission.common');
    Route::put('Report/UpdateMarksObtain/{id}/{marks}', [ReportController::class, 'UpdateMarksObtain'])->middleware('permission.common');
    Route::get('Report/GetResults', [ReportController::class, 'GetResults'])->middleware('permission.common');
    Route::get('Report/GetResultsById/{id}', [ReportController::class, 'GetResultsById'])->middleware('permission.common');
    Route::get('Report/GetFilteredQuiz/{id}', [ReportController::class, 'GetFilteredQuiz'])->middleware('permission.common');
    Route::get('Report/GetResultByTopic/{id}', [ReportController::class, 'GetResultByTopic'])->middleware('permission.common');
    Route::get('Report/GetReportType', [ReportController::class, 'GetReportType'])->middleware('permission.common');
    
    //Settings Controller  
    Route::post('Settings/SentEmailToCheckedStudents', [SettingsController::class, 'SentEmailToCheckedStudents'])->middleware('permission.common');
    Route::post('Settings/SendInvitationMail', [SettingsController::class, 'SendInvitationMail'])->middleware('permission.common');
    Route::get('Settings/ChkEmailConfiguration', [SettingsController::class, 'ChkEmailConfiguration'])->middleware('permission.common');
    Route::put('Settings/UpdateSettings', [SettingsController::class, 'UpdateSettings'])->middleware('permission.common');
    Route::post('Settings/UploadLogo', [SettingsController::class, 'UploadLogo'])->middleware('permission.common');
    Route::post('Settings/UploadFavicon', [SettingsController::class, 'UploadFavicon'])->middleware('permission.common');
    Route::get('Settings/GetFaqList', [SettingsController::class, 'GetFaqList'])->middleware('permission.common');
    Route::post('Settings/CreateFaq', [SettingsController::class, 'CreateFaq'])->middleware('permission.common');
    Route::post('Settings/CreateFaq', [SettingsController::class, 'CreateFaq'])->middleware('permission.common');
    Route::put('Settings/UpdateFaq', [SettingsController::class, 'UpdateFaq'])->middleware('permission.common');
    Route::delete('Settings/DeleteFaq/{id}', [SettingsController::class, 'DeleteFaq'])->middleware('permission.common');
});
