<?php

namespace App\Http\Controllers;

use App\Models\User\Users;
use App\Models\User\UserRole;
use App\Models\User\LogHistory;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{
    use ApiResponser;

    public function GetLoginInfo(Request $request)
    {
        try
        {
            $attr = $request->validate([
                'email' => 'required|string',
                'password' => 'required|string|min:8'
            ]);
    
            if (!Auth::attempt($attr)) {
                return $this->error('Credentials not match', 204,'error');
            }
            $userInfo=DB::table('users')
                        ->join('userroles','users.userRoleId','=','userroles.userRoleId')
                        ->select('users.*','userroles.roleName','userroles.displayName')
                        ->where('users.email','=',$request->email)
                        ->first();
            return $this->successData([
                'token' => auth()->user()->createToken('quick-test-api-token')->plainTextToken,
                'obj'=>$userInfo
            ]);
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }        
    }

    public function GetUserInfoForForgetPassword($email)
    {
        try 
        {
            $user=Users::where('email',$email)->first();
            return $user;
            /* if(Users::where('email',$email)->count()>0){
                $user=Users::where('email',$email)->first();
                return $user;
            }else{
                return $this->error('There is no user for this email', 202);
            } */
        } 
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }

    public function StudentRegistration(Request $request)
    {
        try 
        {
            $request->validate([
                'fullName'=>'required|string',
                'email'=>'required|string|email',
                'password'=>'required|min:8'
                //'addedBy'=>'required'
            ]);

            $duplicateRegistration=false;
            if(!empty($request->stripeSessionId)){
                $chkDuplicateSessionId=Users::where('stripeSessionId',$request->stripeSessionId)->first();
                if($chkDuplicateSessionId!=null){
                    $duplicateRegistration=true;
                }
            }

            $chkDuplicate=Users::where('email',$request->email)->first();
            if($duplicateRegistration==true){
                return $this->error('Please pay first. This is an invalid session!',202,'duplicate');
            }else if($chkDuplicate==null){
                Users::create([
                    'userRoleId'=>2,
                    'fullName'=>$request->fullName,
                    'email'=>$request->email,
                    'password'=>bcrypt($request->password),
                    'stripeSessionId'=>$request->stripeSessionId,
                    'addedBy'=>$request->addedBy
                ]);
                return $this->successMsg('Successfully saved');
            }else{
                return $this->error('This Email already have a user!',202,'duplicate');
            }

        } 
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }

    public function CreateLoginHistory(Request $request)
    {
        try 
        {
            $request->validate([
                'token'=>'required|string',
                'userId'=>'required'
            ]);
            LogHistory::create([
                'logCode'=>$request->token,
                'userId'=>$request->userId,
                'ip'=>$request->ip,
                'browser'=>$request->browser,
                'browserVersion'=>$request->browserVersion,
                'platform'=>$request->platform
            ]);
            return $this->successMsg('Successfully saved');
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }

    public function UpdateLoginHistory($logCode)
    {
        try 
        {
            LogHistory::where('logCode',$logCode)
                        ->update([
                            'logOutTime'=>now()
                        ]);
            return $this->successMsg('Successfully updated');
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }

    public function GetBrowseList()
    {
        try 
        {
            $logInfo=DB::table('users')
                        ->join('loghistories','users.userId','=','loghistories.userId')
                        ->select('users.fullName','users.email','loghistories.logInTime','loghistories.logOutTime','loghistories.ip','loghistories.browser','loghistories.browserVersion','loghistories.platform')
                        ->get();
            return $logInfo;
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }

    public function GetUserList()
    {
        try 
        {
            $users=DB::table('users')
                        ->join('userroles','users.userRoleId','=','userroles.userRoleId')
                        ->select('userroles.roleName','userroles.displayName','users.*')
                        ->get();
            return $users;
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }

    public function GetSingleUser($id)
    {
        try 
        {
            $singleUser=Users::firstWhere('userId',$id);
            return $singleUser;
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }

    public function DeleteSingleUser($id)
    {
        try 
        {
            Users::where('userId',$id)->delete();
            return $this->successMsg('Successfully deleted');
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }

    public function CreateUser(Request $request)
    {
        try 
        {
            $request->validate([
                'userRoleId'=>'required',
                'fullName'=>'required|string',
                'email'=>'required|string|email',
                'password'=>'required|min:8',
                'addedBy'=>'required'
            ]);

            $chkDuplicate=Users::where(strtolower('email'),strtolower($request->email))->first();
            if($chkDuplicate==null){
                Users::create([
                    'userRoleId'=>$request->userRoleId,
                    'fullName'=>$request->fullName,
                    'mobile'=>$request->mobile,
                    'email'=>$request->email,
                    'password'=>bcrypt($request->password),
                    'address'=>$request->address,
                    'dateOfBirth'=>$request->dateOfBirth,
                    'imagePath'=>$request->imagePath,
                    'addedBy'=>$request->addedBy
                ]);
                return $this->successMsg('Successfully saved');
            }
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }

    public function UpdateUser(Request $request)
    {
        try 
        {
            $request->validate([
                'userId'=>'required',
                'userRoleId'=>'required',
                'fullName'=>'required|string',
                'email'=>'required|string|email',
                'lastUpdatedBy'=>'required'
            ]);

            $objUser=Users::where('userId',$request->userId)->first();
            $objCheck=Users::where(strtolower('email'),strtolower($request->email))->first();

            if($objCheck!=null && strtolower($objCheck->email)!=strtolower($objUser->email)){
                return $this->error('This Email already has another user!',202,'duplicate');
            }else{
                Users::where('userId',$request->userId)
                    ->update([
                        'userRoleId'=>$request->userRoleId,
                        'fullName'=>$request->fullName,
                        'mobile'=>$request->mobile,
                        'email'=>$request->email,
                        //'password'=>bcrypt($request->password),
                        'address'=>$request->address,
                        'dateOfBirth'=>$request->dateOfBirth,
                        'imagePath'=>$request->imagePath,
                        'lastUpdatedBy'=>$request->lastUpdatedBy,
                        'lastUpdatedDate'=>now()
                    ]);
                return $this->successMsg('Successfully updated');
            }
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }

    public function ResetUserPassword(Request $request)
    {
        try 
        {
            $request->validate([
                'email'=>'required|string',
                'hassPassword'=>'required|string',
                'newPassword'=>'required|string'
            ]);
            $objUser=Users::where('email',$request->email)
            ->first();
            if($objUser!=null){
                Users::where('email',$request->email)
                ->update([
                    'password'=>bcrypt($request->newPassword)
                ]);
                return $this->successMsg('Successfully updated');
            }else{
                return $this->error('Not Successful',202,'error'); 
            }
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }

    public function UpdateUserProfile(Request $request)
    {
        try 
        {
            $request->validate([
                'userId'=>'required',
                'fullName'=>'required|string',
                'lastUpdatedBy'=>'required'
            ]);

            Users::where('userId',$request->userId)
                    ->update([
                        'fullName'=>$request->fullName,
                        'mobile'=>$request->mobile,
                        'address'=>$request->address,
                        'dateOfBirth'=>$request->dateOfBirth,
                        'imagePath'=>$request->imagePath,
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

    public function Unlock(Request $request)
    {
        try
        {
            $request->validate([
                'password'=>'required|string',
                'hashedPassword'=>'required|string',
            ]);
            if(Hash::check($request->password, $request->hashedPassword))
            {
                return $this->successMsg('Password matched');
            }
            else
            {
                return $this->error('Password not matched',202,'error');
            }
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }

    public function ChangeUserPassword(Request $request)
    {
        try 
        {
            $request->validate([
                'password'=>'required|min:8'
            ]);

            Users::where('userId',$request->userId)
                    ->update([
                        'password'=>bcrypt($request->password)
                    ]);
            return $this->successMsg('Successfully updated');
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }

    public function Upload(Request $request) 
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
        $path=public_path().'/Upload/ProfileImages';
        $fileName=time().$file->getClientOriginalName();
        $file->move($path, $fileName);
        $pathReturn='\\Upload\\ProfileImages\\'.$fileName;
        return $this->successData($pathReturn);
    }

    public function GetUserRoleList()
    {
        try 
        {
            $roles=UserRole::all();
            return $roles;
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }

    public function DeleteSingleRole($id)
    {
        try 
        {
            if($id==1 || $id==2){
                return $this->error('This Role is restricted to delete.',202,'restricted');
            }else{
                UserRole::where('userRoleId',$id)->delete();
                return $this->successMsg('Successfully deleted');
            }
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }

    public function CreateUserRole(Request $request)
    {
        try 
        {
            $request->validate([
                'roleName'=>'required|string',
                'displayName'=>'required|string',
                'addedBy'=>'required'
            ]);

            $chkDuplicate=UserRole::where(strtolower('roleName'),strtolower($request->roleName))->first();
            if($chkDuplicate==null){
                UserRole::create([
                    'roleName'=>$request->roleName,
                    'displayName'=>$request->displayName,
                    'roleDesc'=>$request->roleDesc,
                    'addedBy'=>$request->addedBy
                ]);
                return $this->successMsg('Successfully saved');
            }
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }

    public function UpdateUserRole(Request $request)
    {
        try
        {
            $request->validate([
                'userRoleId'=>'required',
                'roleName'=>'required|string',
                'displayName'=>'required|string',
                'lastUpdatedBy'=>'required'
            ]);

            $objUserRole=UserRole::where('userRoleId',$request->userRoleId)->first();
            $objCheck=UserRole::where(strtolower('roleName'),strtolower($request->roleName))->first();

            if($objCheck!=null && strtolower($objCheck->roleName)!=strtolower($objUserRole->roleName)){
                return $this->error('Duplicate User Role name!',202,'duplicate');
            }else if($request->userRoleId==1 || $request->userRoleId==2){
                UserRole::where('userRoleId',$request->userRoleId)
                ->update([
                    'displayName'=>$request->displayName,
                    'roleDesc'=>$request->roleDesc,
                    'lastUpdatedBy'=>$request->lastUpdatedBy,
                    'lastUpdatedDate'=>now()
                ]);
                return $this->successMsg('Successfully updated');
            }else{
                UserRole::where('userRoleId',$request->userRoleId)
                ->update([
                    'roleName'=>$request->roleName,
                    'displayName'=>$request->displayName,
                    'roleDesc'=>$request->roleDesc,
                    'lastUpdatedBy'=>$request->lastUpdatedBy,
                    'lastUpdatedDate'=>now()
                ]);
                return $this->successMsg('Successfully updated');
            }
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }
}
