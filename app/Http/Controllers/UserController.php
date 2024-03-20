<?php

namespace App\Http\Controllers;

use App\Http\Controllers\responseTrait;
use App\Http\Requests\registerRequest;
use App\Mail\VerificationCodeMail;
use App\Models\User;
use App\Models\VerificationCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    use responseTrait;

    //REGISTER METHOD -POST
    public function register(registerRequest $request){
        // Delete all old code that user send before.
        VerificationCode::where('email', $request->email)->delete();
        //Generate new code
        $data['email']=$request->email;
        $data['code'] = mt_rand(100000, 999999);
        $codeData = VerificationCode::create($data);
        User::query()->create([
            'username'=>$request['username'],
            'email'=>$request['email'],
            'password'=>Hash::make($request->password),
            'role'=>$request['role'],
        ]);
        Mail::to($request->email)->send(new VerificationCodeMail($codeData->code));
        return $this->apiResponse([],'Verification Code sent to your email',200);
    }

    public function cheackCode(Request $request){
        $request->validate([
            'code' => ['required','string','exists:verification_codes'],
        ]);

        $ver_code = VerificationCode::firstwhere('code',$request->code);
        // check if it does not expired: the time is one hour
        if ($ver_code->created_at->addHour() < now()) {
            VerificationCode::where('code',$ver_code->code)->delete();
            return $this->apiResponse([],'verification.code_is_expire',422);
        }
        // find user's email
        $user = User::firstWhere('email', $ver_code->email);
        $token=$user->createToken("API TOKEN")->plainTextToken;
        $data =[];
        $data['user']= $user;
        $data['token']=$token;
        $user->update(['is_verified'=>true]);
        $user->is_verified=true;
        return $this->apiResponse($data,'user create successfully',200);
    }
    public function login(Request $request){
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);
        $login = $request->input('login');
        $password = $request->input('password');

        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if(!Auth::attempt([$fieldType=>$login,'password'=>$password])){
            $message='Email & password does not match with our record.';
            return $this->apiResponse([],$message,401);
        }

        $user = User::where($fieldType, $login)->first();

        if($user->is_verified){
            $token = $user->createToken("API TOKEN")->plainTextToken;
            $data['user']=$user;
            $data['token']=$token;
            return $this->apiResponse($data,'user logged in successfully',200);}
        else
        return $this->apiResponse(null,'Your account is not verified. Please verify your account first. then login',401);
    }

    public function logout()
    {
        Auth::user()->currentAccessToken()->delete();
        return $this->apiResponse([],'user logged out successfully',200);
    }
}
