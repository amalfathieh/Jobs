<?php

namespace App\Http\Controllers;

use App\Http\Requests\registerRequest;
use App\Mail\VerificationCodeMail;
use App\Models\User;
use App\Models\VerificationCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
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
        return response()->json(['message'=>'Verification Code sent to your email'],200);
    }

    public function cheackCode(Request $request){
        $request->validate([
            'code' => ['required','string','exists:verification_codes'],
        ]);

        $ver_code = VerificationCode::firstwhere('code',$request->code);
        // check if it does not expired: the time is one hour
        if ($ver_code->created_at->addHour() < now()) {
            VerificationCode::where('code',$ver_code->code)->delete();
            return response(['message' => 'verification.code_is_expire'], 422);
        }
        // find user's email
        $user = User::firstWhere('email', $ver_code->email);
        $token=$user->createToken("API TOKEN")->plainTextToken;
        $data =[];
        $data['user']= $user;
        $data['token']=$token;
        $user->is_verified=true;
        return response()->json([
            'status'=>1,
            'data'=>$data,
            'message'=>'user create successfully'
        ]);
    }
}
