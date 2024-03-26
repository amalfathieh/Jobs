<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CompanyController extends Controller
{
    use responseTrait;
    public function createCompany(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                // 'user_id' => 'unique:companies,user_id',
                'company_name' => 'required|string',
                'logo' => 'image',
                'location' => 'required',
                'about' => 'required',
                'contact_info' => 'required'
            ]);

            if ($validate->fails()) {
                return $this->apiResponse(null, $validate->errors(), 400);
            }

            $user = User::where('id', Auth::user()->id)->first();
            $logo = Str::random(3) . 'Profile.' . $request->logo->getClientOriginalExtension();
            Storage::disk('public')->put($logo, file_get_contents($request->logo));

            Company::create([
                'user_id' => $user->id,
                'company_name' => $request->company_name,
                'logo' => $logo,
                'location' => $request->location,
                'about' => $request->about,
                'contact_info' => $request->contact_info
            ]);
            return $this->apiResponse(null, 'success',  201);
        } catch (\Exception $ex) {
            return $this->apiResponse(null, $ex->getMessage(), 500);
        }
    }
}
