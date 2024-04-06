<?php

namespace App\Http\Controllers;

use App\Http\Controllers\responseTrait;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\RePasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Jobs\ForgotPasswordJob;
use App\Jobs\MailJob;
use App\Models\ResetCodePassword;
use App\Models\User;
use App\Models\VerificationCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
class UserController extends Controller
{
    use responseTrait;

    //REGISTER METHOD -POST
    public function register(RegisterRequest $request)
    {

        // Delete all old code that user send before.
        VerificationCode::where('email', $request->email)->delete();
        //Generate new code
        $data['email'] = $request->email;
        $data['code'] = mt_rand(100000, 999999);
        $codeData = VerificationCode::create($data);
        User::query()->create([
            'user_name' => $request['user_name'],
            'email' => $request['email'],
            'password' => Hash::make($request->password),
            'role' => $request['role'],
        ]);
        MailJob::dispatch($request->email, $request->code);
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
        $user->is_verified = true;
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
            $user->tokens()->delete();
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
            MailJob::dispatch($request->email, $request->code);
            return $this->apiResponse([], 'Verification Code sent to your email', 200);
        } catch (\Exception $ex) {
            return $this->apiResponse(null, $ex->getMessage(), 500);
        }
    }

    public function resetPassword(ResetPasswordRequest $request) {

        $user = User::where('id', Auth::user()->id)->first();
        $user->password = Hash::make($request->password);
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

    // Delete Account
    public function delete() {
        $user = User::where('id', Auth::user()->id)->first();

        if ($user->delete()) {
            $user->tokens()->delete();
            return $this->apiResponse(null, 'Account Deleted Successfully!', 200);
        }
        return $this->apiResponse(null, "Something went wrong", 500);
    }
}
