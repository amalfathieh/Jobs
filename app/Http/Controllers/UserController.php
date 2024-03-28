<?php

namespace App\Http\Controllers;

use App\Http\Controllers\responseTrait;
use App\Http\Requests\registerRequest;
use App\Mail\ForgotPassword;
use App\Mail\VerificationCodeMail;
use App\Models\ResetCodePassword;
use App\Models\User;
use App\Models\VerificationCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    use responseTrait;

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
            'password' => Hash::make($request->password),
            'role' => $request['role'],
        ]);
        Mail::to($request->email)->send(new VerificationCodeMail($codeData->code));
        return $this->apiResponse([], 'Verification Code sent to your email', 200);
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
            return $this->apiResponse([], 'verification.code_is_expire', 422);
        }
        // find user's email
        $user = User::firstWhere('email', $ver_code->email);
        $token = $user->createToken("API TOKEN")->plainTextToken;
        $data = [];
        $data['user'] = $user;
        $data['token'] = $token;
        $user->update(['is_verified' => true]);
        $user->is_verified = true;
        VerificationCode::where('code', $ver_code->code)->delete();
        return $this->apiResponse($data, 'user create successfully', 200);
    }
    public function login(Request $request)
    {

        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);
        $login = $request->input('login');

        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (!Auth::attempt([$fieldType => $login, 'password' => $request['password']])) {
            $message = 'Email & password does not match with our record.';
            return $this->apiResponse([], $message, 401);
        }

        $user = User::where($fieldType, $login)->first();

        if ($user->is_verified) {
            $token = $user->createToken("API TOKEN")->plainTextToken;
            $data['user'] = $user;
            $data['token'] = $token;
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
            Mail::to($request->email)->send(new VerificationCodeMail($codeData->code));
            return $this->apiResponse([], 'Verification Code sent to your email', 200);
        } catch (\Exception $ex) {
            return $this->apiResponse(null, $ex->getMessage(), 500);
        }
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

            return $this->apiResponse([], 'We sent code to your email. Check your email please', 200);
        } catch (\Exception $ex) {
            return $this->apiResponse([], $ex->getMessage(), 500);
        }
    }

    public function resetPassword(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'code' => 'required|string|exists:reset_code_passwords',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required|string|min:6|same:password'
        ]);

        if ($validate->fails()) {
            return $this->apiResponse([], $validate->errors(), 400);
        }
        if ($request->password != $request->password_confirmation)
            return $this->apiResponse([], 'The password confirmation does not match.please re-enter it correctly.', 400);
        $passwordReset = ResetCodePassword::firstWhere('code', $request->code);

        if ($passwordReset->created_at->addHour() < now()) {
            $passwordReset->delete();
            return $this->apiResponse([], 'password code_is_expire', 422);
        }

        $user = User::firstWhere('email', $passwordReset->email);

        $request['password'] = bcrypt($request['password']);
        $user->update([
            'password' => $request->password,
        ]);
        $passwordReset->delete();

        return $this->apiResponse([], 'password has been successfully reset', 200);
    }
}
