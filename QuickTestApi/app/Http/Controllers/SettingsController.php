<?php

namespace App\Http\Controllers;

use App\Models\User\Users;
use App\Models\Others\Faq;
use App\Models\Others\SiteSetting;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Mail;

class SettingsController extends Controller
{
    use ApiResponser;

    public function SendPasswordResetLink(Request $request)
    {
        try
        {
            if($this->emailConfiguration()==false){
                return $this->error('Not successful! Email Configuration not set yet.',202,'error');
            }else{
                $email=$request->toEmail;
                $data = array('logoPath'=>$request->logoPath,'email'=>$request->toEmail,
                'siteUrl'=>$request->siteUrl,'body'=>$request->body,'header'=>'Use below link to reset your existing password');

                Mail::send('invitation', $data, function($message) use ($email) {
                    $message->to($email, 'Hello')->subject('Password Reset');
                    $message->from(env('MAIL_USERNAME'),env('MAIL_FROM_NAME'));
                }); 
                return $this->successMsg('Password reset link has sent! Please Check your email.');
            }
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }

    public function SendWelcomeMail(Request $request)
    {
        try
        {
            if($this->emailConfiguration()==false){
                return $this->error('Not successful! Email Configuration not set yet.',202,'error');
            }else{
                $email=$request->toEmail;
                $name=$request->name;
                $data = array('toEmail'=>$request->toEmail,'name'=>$request->name,
                'logoPath'=>$request->logoPath,'siteUrl'=>$request->siteUrl,'body'=>$request->body,
                'header'=>'Welcome,We found you as a new member');
                Mail::send('welcome', $data, function($message) use ($email,$name) {
                    $message->to($email, $name)->subject('Welcome');
                    $message->from(env('MAIL_USERNAME'),env('MAIL_FROM_NAME'));
                });      
                return $this->successMsg('Successfully sent');
            }
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }

    public function SentEmailToCheckedStudents(Request $request)
    {
        try
        {
            if($this->emailConfiguration()==false){
                return $this->error('Not successful! Email Configuration not set yet.',202,'error');
            }else{
                $invitationEmails=$request->all();
                $emails=[];
                for($i=0;$i<count($invitationEmails);$i++){
                    $emails[]=$invitationEmails[$i]['email'];
                }
                $data = array('logoPath'=>$request[0]['logoPath'],'email'=>$request[0]['email'],
                'siteUrl'=>$request[0]['siteUrl'],'body'=>$request[0]['body'],
                'header'=>'Hello,A message from Admin is waiting for you!');

                $subject=$request[0]['subject'];

                Mail::send('invitation', $data, function($message) use ($emails,$subject) {
                    $message->to($emails, 'Invited User')->subject($subject);
                    $message->from(env('MAIL_USERNAME'),env('MAIL_FROM_NAME'));
                });      
                return $this->successMsg('Successfully sent');
            }
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }

    public function SendInvitationMail(Request $request)
    {
        try
        {
            if($this->emailConfiguration()==false){
                return $this->error('Not successful! Email Configuration not set yet.',202,'error');
            }else{
                $invitationEmails=$request->all();
                $emails=[];
                for($i=0;$i<count($invitationEmails);$i++){
                    $emails[]=$invitationEmails[$i]['email'];
                }
                $data = array('logoPath'=>$request[0]['logoPath'],'email'=>$request[0]['email'],
                'siteUrl'=>$request[0]['siteUrl'],'body'=>$request[0]['body'],'header'=>'Hello,An Invitation is waiting for you!');

                Mail::send('invitation', $data, function($message) use ($emails) {
                    $message->to($emails, 'Invited User')->subject('Invitation to attend the Test');
                    $message->from(env('MAIL_USERNAME'),env('MAIL_FROM_NAME'));
                });      
                return $this->successMsg('Successfully sent');
            }
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }

    public function ChkEmailConfiguration()
    {
        try
        {
            if(env('MAIL_MAILER')==null || env('MAIL_HOST')==null || env('MAIL_PORT')==null ||
            env('MAIL_USERNAME')==null || env('MAIL_PASSWORD')==null || env('MAIL_ENCRYPTION')==null){
                return false;
            }
            return true;
        }
        catch(\Exception $ex)
        {
            return false;
        }
    }

    public function GetSiteSettings()
    {
        try
        {
            $data=SiteSetting::first();
            return $data;
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }

    public function UpdateSettings(Request $request)
    {
        try
        {
            $request->validate([
                'siteTitle'=>'required|string'
            ]);
            SiteSetting::where('siteSettingsId',$request->siteSettingsId)
            ->update([
                'siteTitle'=>$request->siteTitle,
                'welComeMessage'=>$request->welComeMessage,
                'copyRightText'=>$request->copyRightText,
                'logoPath'=>$request->logoPath,
                'faviconPath'=>$request->faviconPath,
                'appBarColor'=>$request->appBarColor,
                'footerColor'=>$request->footerColor,
                'bodyColor'=>$request->bodyColor,
                'allowWelcomeEmail'=>$request->allowWelcomeEmail,
                'allowFaq'=>$request->allowFaq,
                'endExam'=>$request->endExam,
                'logoOnExamPage'=>$request->logoOnExamPage,
                'paidRegistration'=>$request->paidRegistration,
                'registrationPrice'=>$request->registrationPrice,
                'currency'=>$request->currency,
                'currencySymbol'=>$request->currencySymbol,
                'stripePubKey'=>$request->stripePubKey,
                'stripeSecretKey'=>$request->stripeSecretKey,
                'clientUrl'=>$request->clientUrl,
                'defaultEmail'=>$request->defaultEmail,
                'host'=>$request->host,
                'password'=>$request->password,
                'port'=>$request->port,
                'lastUpdatedBy'=>$request->lastUpdatedBy,
                'lastUpdatedDate'=>now()
            ]);
            return $this->successMsg('Successfully saved');
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }

    public function UpdateClientUrl(Request $request)
    {
        try
        {
            $model=SiteSetting::first();
            $model->clientUrl=$request->displayName;
            $model->save();
            return $this->successMsg('Successfully saved');
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }

    public function UploadLogo(Request $request) 
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
        $path=public_path().'/Upload/Logo';
        $fileName=time().$file->getClientOriginalName();
        $file->move($path, $fileName);
        $pathReturn='\\Upload\\Logo\\'.$fileName;
        return $this->successData($pathReturn);
    }

    public function UploadFavicon(Request $request) 
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
        $path=public_path().'/Upload/Favicon';
        $fileName=time().$file->getClientOriginalName();
        $file->move($path, $fileName);
        $pathReturn='\\Upload\\Favicon\\'.$fileName;
        return $this->successData($pathReturn);
    }

    public function GetFaqList()
    {
        try
        {
            $data=Faq::all();
            return $data;
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }

    public function DeleteFaq($id)
    {
        try
        {
            Faq::where('faqId',$id)->delete();
            return $this->successMsg('Successfully deleted');
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }

    public function CreateFaq(Request $request)
    {
        try
        {
            $request->validate([
                'title'=>'required|string',
                'description'=>'required|string',
                'addedBy'=>'required'
            ]);

            Faq::create([
                'title'=>$request->title,
                'description'=>$request->description,
                'addedBy'=>$request->addedBy
            ]);
            return $this->successMsg('Successfully saved');
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }

    public function UpdateFaq(Request $request)
    {
        try
        {
            $request->validate([
                'title'=>'required|string',
                'description'=>'required|string',
                'lastUpdatedBy'=>'required'
            ]);

            $objFaq=Faq::where('faqId',$request->faqId)->first();
            $objCheck=Faq::where(strtolower('title'),strtolower($request->title))->first();
            
            if($objCheck!=null && strtolower($objCheck->title)!=strtolower($objFaq->title)){
                return $this->error('Duplicate FAQ!',202,'duplicate');
            }else{
                Faq::where('faqId',$request->faqId)
                ->update([
                    'title'=>$request->title,
                    'description'=>$request->description,
                    'lastUpdatedBy'=>$request->addedBy,
                    'lastUpdatedDate'=>now()
                ]);
            }
            return $this->successMsg('Successfully updated');
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }
}
