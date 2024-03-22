<?php

namespace App\Http\Controllers;

use App\Http\Requests\registerRequest;
use App\Mail\ForgotPassword;
use App\Mail\VerificationCodeMail;
use App\Models\ResetCodePassword;
use App\Models\User;
use App\Models\VerificationCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    //REGISTER METHOD -POST
    public function register(registerRequest $request)
    {
        // Delete all old code that user send before.
        VerificationCode::where('email', $request->email)->delete();
        //Generate new code
        $data['email'] = $request->email;
        $data['code'] = mt_rand(100000, 999999);
        $codeData = VerificationCode::create($data);
        User::query()->create([
            'username' => $request['username'],
            'email' => $request['email'],
            'password' => Hash::make($request['password']),
            'role' => $request['role'],
        ]);
        Mail::to($request->email)->send(new VerificationCodeMail($codeData->code));
        return response()->json(['message' => 'Verification Code sent to your email'], 200);
    }

    public function checkCode(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'exists:verification_codes'],
        ]);

        $ver_code = VerificationCode::firstwhere('code', $request->code);
        // check if it does not expired: the time is one hour
        if ($ver_code->created_at->addHour() < now()) {
            VerificationCode::where('code', $ver_code->code)->delete();
            return response(['message' => 'verification.code_is_expire'], 422);
        }
        // find user's email
        $user = User::firstWhere('email', $ver_code->email);
        $token = $user->createToken("API TOKEN")->plainTextToken;
        $data = [];
        $data['user'] = $user;
        $data['token'] = $token;
        return response()->json([
            'status' => "success",
            'data' => $data,
            'message' => 'user create successfully'
        ]);
    }

    // Reset Password

    public function sendCode(Request $request)
    {
        try {
            $data = $request->validate([
                'email' => 'required|email|exists:users',
            ]);

            ResetCodePassword::where('email', $request->email)->delete();

            $data['code'] = mt_rand(100000, 999999);

            $codeData = ResetCodePassword::create($data);

            Mail::to($request->email)->send(new ForgotPassword($codeData->code));

            return response()->json(['message' => 'We sent code to your email. Check your email please'], 200);
        } catch (\Exception $ex) {
            return response()->json([
                'status' => 'error',
                'message' => $ex->getMessage()
            ], 500);
        }
    }

    public function checkCodeForResetPassword(Request $request)
    {
        $request->validate([
            'code' => 'required|string|exists:reset_code_passwords',
            'password' => 'required|string|min:6',
        ]);

        $passwordReset = ResetCodePassword::firstWhere('code', $request->code);

        if ($passwordReset->created_at > now()->addHour()) {
            $passwordReset->delete();
            return response()->json(['message' => 'password code_is_expire'], 422);
        }

        $user = User::firstWhere('email', $passwordReset->email);

        $user->update($request->only('password'));

        $passwordReset->delete();

        return response()->json(['message' => 'password has been successfully reset'], 200);
    }
}
