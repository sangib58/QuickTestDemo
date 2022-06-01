<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Others\SiteSetting;
use App\Models\User\Users;
use App\Models\Quiz\QuizTopic;
use App\Models\Quiz\QuizPayment;
use App\Traits\ApiResponser;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use Stripe;
use Session;


class PaymentsController extends Controller
{
  use ApiResponser;

  public function indexRegistration(Request $request)
  {
    $data=SiteSetting::first();
    session(['stripeSecretKey'=>$data->stripeSecretKey,'registrationPrice'=>$data->registrationPrice,
    'currency'=>$data->currency,'logoPath'=>$data->logoPath,'clientUrl'=>$data->clientUrl]);

    return view('registration',['clientUrl'=>$request->location,'stripePubKey'=>$data->stripePubKey]);
  }

  public function registrationPayment(Request $request)
  {
    try
    {
      $chkDuplicate=Users::where(strtolower('email'),strtolower($request->customerEmail))->first();
      
      if($this->emailConfiguration()==false){
        Session::flash('success', 'Not successful! Email Configuration not set yet.');
      }else if($chkDuplicate!=null){
        Session::flash('success', 'This email already have a user. Please try with a different email.');
      }else{    
        Stripe\Stripe::setApiKey(session('stripeSecretKey'));
        Stripe\Charge::create ([
          "amount" => session('registrationPrice') * 100,
          "currency" => session('currency'),
          "source" => $request->stripeToken,
          "description" => "Payment done." 
        ]);
         
        $email=$request->customerEmail;
        $name=$request->customerName;
        $mailBody='From now you can sign in using '.$request->customerEmail.' as email and 12345678 as password';

        $data = array('toEmail'=>$request->customerEmail,'name'=>$request->customerName,
        'logoPath'=>session('logoPath'),'siteUrl'=>session('clientUrl'),'body'=>$mailBody,
        'header'=>'Welcome,We found you as a new member');

        Mail::send('welcome', $data, function($message) use ($email,$name) {
          $message->to($email, $name)->subject('Hello from Admin');
          $message->from(env('MAIL_USERNAME'),env('MAIL_FROM_NAME'));
        });
        
        Users::create([
          'userRoleId'=>2,
          'fullName'=>$request->customerName,
          'email'=>$request->customerEmail,
          'password'=>bcrypt('12345678')
        ]);
  
        Session::flash('success', 'Payment successfully done! Please check your mail. we sent your credential there.');
      }       
      return back();
    }
    catch(\Exception $ex)
    {
      Session::flash('error', 'Transaction not successful ! Please try again.');
      return back();
    }
  }

  public function indexPayment(Request $request)
  {
    $data=SiteSetting::first();
    session(['stripeSecretKey'=>$data->stripeSecretKey,
    'quizTopicId'=>$request->quizTopicId,'addedBy'=>$request->addedBy,
    'location'=>$request->location,'quizEmail'=>$request->quizEmail,'currency'=>$request->currency]);

    return view('quizPayment',['stripePubKey'=>$data->stripePubKey]);
  }

  public function quizPayment(Request $request)
  {
    try
    {
      $quizData=QuizTopic::where('quizTopicId',session('quizTopicId'))->first();
      if($quizData!=null){
        Stripe\Stripe::setApiKey(session('stripeSecretKey'));
        Stripe\Charge::create ([
          "amount" => $quizData->quizPrice * 100,
          "currency" => session('currency'),
          "source" => $request->stripeToken,
          "description" => "Payment done." 
        ]);
        
        QuizPayment::create([
          'quizTopicId'=>session('quizTopicId'),
          'email'=>session('quizEmail'),
          'currency'=>session('currency'),
          'addedBy'=>session('addedBy')
        ]);
        Session::flash('success', 'Payment successfully done !');
      }else{
        Session::flash('success', 'This Test not exists. Please try with a different test.');
      }       
      return back();
    }
    catch(\Exception $ex)
    {
      Session::flash('error', 'Transaction not successful ! Please try again.');
      return back();
    }
  }
}
