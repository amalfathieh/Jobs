<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\RePasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Resources\UserResource;
use App\Jobs\ChangeEmailJob;
use App\Jobs\ForgotPasswordJob;
use App\Jobs\MailJob;
use App\Models\ResetCodePassword;
use App\Models\Seeker;
use App\Models\User;
use App\Models\VerificationCode;
use App\Notifications\SendNotification;
use App\Traits\responseTrait;
use App\Traits\NotificationTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use Laravel\Socialite\Facades\Socialite;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    use responseTrait,NotificationTrait;

    //REGISTER METHOD -POST
    public function register(RegisterRequest $request)
    {

        // Delete all old code that user send before.
        VerificationCode::where('email', $request->email)->delete();
        //Generate new code
        $data['email'] = $request->email;
        $data['code'] = mt_rand(100000, 999999);
        $codeData = VerificationCode::create($data);
        $user = User::query()->create([
            'user_name' => $request['user_name'],
            'email' => $request['email'],
            'password' => $request->password,
            'roles_name' => ['user' ,$request['roles_name']],
        ]);
        $user->syncRoles($user->roles_name);
        MailJob::dispatch($request->email, $data['code']);
        return $this->apiResponse([], 'Verification Code sent to your email', 200);
    }

    public function verifyAccount(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'exists:verification_codes'],
        ]);

        $ver_code = VerificationCode::firstwhere('code', $request->code);
        // check if it does not expired: the time is one hour
        if ($ver_code->created_at->addHour() < now()) {
            VerificationCode::where('code', $ver_code->code)->delete();
            return $this->apiResponse([], 'verification.code_is_expire', 422);
        }
        // find user's email
        $user = User::firstWhere('email', $ver_code->email);
        $token = $user->createToken("API TOKEN")->plainTextToken;
        $data = [];
        $data['user'] = $user;
        $data['token'] = $token;
        $user->update(['is_verified' => true]);
        // $user->is_verified = true;
        VerificationCode::where('code', $ver_code->code)->delete();
        return $this->apiResponse($data, 'user create successfully', 200);
    }

    public function login(LoginRequest $request)
    {

        $login = $request->input('login');
        $password =  $request->input('password');

        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'user_name';

        if (!Auth::attempt([$fieldType => $login, 'password' => $password])) {
            $message = 'Email & password does not match with our record.';
            return $this->apiResponse([], $message, 401);
        }

        $user = User::where($fieldType, $login)->first();
        if ($user->is_verified) {
            $token = $user->createToken("API TOKEN")->plainTextToken;
            $data['user'] = $user;
            $data['token'] = $token;

            $role = [];
            foreach ($user->roles_name as $ro) {
                $role[$ro] = Role::findByName($ro, 'web')->permissions->pluck('name');
            }

            $data['user']->roles_name = $role;

            //send notification to admin when employee login to dashboard
            if($user->hasRole('employee')){
                $admin = User::role('owner')->first();
                $tokens = $admin->routeNotificationForFcm();
                $data =[
                    'obj_id'=>$user->id,
                    'title'=>'Login Alert',
                    'body'=>'New login to the dashboard has been detected. User: '.$user->employee->first_name.' '.$user->employee->last_name,
                ];
                Notification::send($admin,new SendNotification($data));
                $this->sendPushNotification($data['title'],$data['body'],$tokens);
            }

            return $this->apiResponse($data, 'user logged in successfully', 200);
        } else
            return $this->apiResponse(null, 'Your account is not verified. Please verify your account first. then login', 401);
    }

    public function logout()
    {
        request()->user()->currentAccessToken()->delete();
        return $this->apiResponse([], 'user logged out successfully', 200);
    }

    public function sendCodeVerification(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'email' => ['required', 'email:rfc,dns']
            ]);

            if ($validate->fails()) {
                return $this->apiResponse(null, $validate->errors(), 400);
            }

            VerificationCode::where('email', $request->email)->delete();
            //Generate new code
            $data['email'] = $request->email;
            $data['code'] = mt_rand(100000, 999999);
            $codeData = VerificationCode::create($data);
            MailJob::dispatch($request->email, $data['code']);
            return $this->apiResponse([], 'Verification Code sent to your email', 200);
        } catch (\Exception $ex) {
            return $this->apiResponse(null, $ex->getMessage(), 500);
        }
    }

    public function checkPassword(Request $request) {
        $user = User::where('id', Auth::user()->id)->first();
        return password_verify($request->password, $user->password)?
            $this->apiResponse(null, 'Password is correct', 200):

            $this->apiResponse(null, 'Password is incorrect', 401);
    }

    public function resetPassword(ResetPasswordRequest $request) {

        $user = User::where('id', Auth::user()->id)->first();
        $user->password = Hash::make($request->password);
        if($user->hasRole('employee')){
            $employee = $user->employee;
            $employee->is_change_password = true;
            $employee->save();
        }
        $user->save();

        return $this->apiResponse([], 'password has been successfully reset', 200);
    }


    // Reset Password When user forgot his password

    public function sendCode(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'email' => 'required|email:rfc,dns|exists:users',
            ]);

            if ($validate->fails()) {
                return $this->apiResponse(null, $validate->errors(), 422);
            }

            $user = User::where('email', $request->email)->first();
            if (!$user->is_verified) {
                return $this->apiResponse(null, 'Your account is not verified', 400);
            }

            ResetCodePassword::where('email', $request->email)->delete();

            $data['email'] = $user->email;
            $data['code'] = mt_rand(100000, 999999);

            $codeData = ResetCodePassword::create($data);

            ForgotPasswordJob::dispatch($data['email'], $data['code']);

            return $this->apiResponse([], 'We sent code to your email. Check your email please', 200);
        } catch (\Exception $ex) {
            return $this->apiResponse([], $ex->getMessage(), 500);
        }
    }

    public function checkCode(Request $request) {
        $validate = Validator::make($request->all(),[
            'code' => ['required', 'integer', 'exists:reset_code_passwords'],
        ]);
        if ($validate->fails()) {
            return $this->apiResponse(null, $validate->errors(), 400);
        }

        $ver_code = ResetCodePassword::firstwhere('code', $request->code);
        // check if it does not expired: the time is one hour
        if ($ver_code->created_at->addHour() < now()) {
            ResetCodePassword::where('code', $ver_code->code)->delete();
            return $this->apiResponse(null, 'code has expired', 422);
        }
        return $this->apiResponse(null, 'code is correct', 200);
    }

    public function rePassword(RePasswordRequest $request)
    {

        $passwordReset = ResetCodePassword::firstWhere('code', $request->code);

        if ($passwordReset->created_at->addHour() < now()) {
            $passwordReset->delete();
            return $this->apiResponse([], 'code has expired', 422);
        }

        $user = User::firstWhere('email', $passwordReset->email);

        $request['password'] = bcrypt($request['password']);
        $user->update([
            'password' => $request->password,
        ]);
        $passwordReset->delete();

        return $this->apiResponse([], 'password has been successfully reset', 200);
    }


    public function update(Request $request) {
        $user = User::where('id', Auth::user()->id)->first();
        // if ($request->email) {
        //     $data['code'] = mt_rand(100000, 999999);
        //     $data['pre_email'] = $user->email;
        //     $data['new_email'] = $request->email;
        //     ChangeEmailJob::dispatch($data);
        // }
        if ($user->update($request->all())){
            return $this->apiResponse($user, 'Updated successfully', 200);
        }
        return $this->apiResponse(null, 'Something went wrong', 500);
    }

    // Delete Account
    public function delete() {
        $user = User::where('id', Auth::user()->id)->first();

        if ($user->delete()) {
            $user->tokens()->delete();
            return $this->apiResponse(null, 'Account Deleted Successfully!', 200);
        }
        return $this->apiResponse(null, "Something went wrong", 500);
    }


    public function storeToken(Request $request){
        $user = Auth::user();
        $exists = $user->deviceTokens()->where('token',$request->token)->exists();
        if(!$exists) {
            $user->deviceTokens()->create([
                'token' => $request->token,
            ]);
            return $this->apiResponse(null, 'success', 200);
        }
    }

    public function noti(){
        return $this->sendPushNotification('test notification','this is new notificatino', 'fNtgp5QlTPGtB4xuCw7K-U:APA91bF0pi2GMfD3xIXHjMYSmhwPeFBdHGcsQ4_lYNmWafRYq_WCOmz_knTYbVxnhjoy8IMyJJUdYq08dCBi3df-ENhHcqV5j6tRB5u0qxHNRF9l7khkQAgkt6j8ULMd4lXAJS3IBFa3');
    }

    public function search($search){
        $users = User::where(function ($query) use ($search){
            $query->where('user_name', 'LIKE', '%' . $search . '%');

        })->orWhereHas('seeker', function ($query) use ($search) {
            $query->where('first_name', 'LIKE', '%' . $search . '%')
            ->orWhere('last_name', 'LIKE', '%' . $search . '%');

        })->orWhereHas('company', function ($query) use ($search) {
            $query->where('company_name', 'LIKE', '%' . $search . '%');
        })->get();

        $users = $users->reject(function(User $user) {
            $roles = $user->roles_name;
            foreach ($roles as $value) {
                return $value === 'owner';
            }
        });

        if($users->isEmpty()){
            return $this->apiResponse(null,'Not Found',404);

        } else{
            $result = UserResource::collection($users);
        }
        return $result;
    }

    public function testStore(){
        try {
            $users=User::where('id','!=',auth()->user()->id)->get();
            $user2 = User::find(1);
            $data =[
                'obj_id'=>1,
                'title'=>'Login',
                'body'=>'to22 notification',
            ];
            Notification::send($user2,new SendNotification($data));
        } catch (\Exception $ex) {
            return $this->apiResponse(null, $ex->getMessage(), 500);
        }
        return $this->apiResponse($data, "sent successfully", 200);
    }

    public function redirect() {
        return Socialite::driver('google')->redirect();
    }

    public function callbackGoogle() {
        try {
            $google_user = Socialite::driver('google')->user();
            $user = User::where('google_id', $google_user->getId())->first();

            if (!$user) {

            }
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
}
