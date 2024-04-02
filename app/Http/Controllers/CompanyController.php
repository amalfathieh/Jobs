<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompanyRequest;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\AuthorizationException;

class CompanyController extends Controller
{
    use responseTrait;
    public function createCompany(CompanyRequest $request)
    {
        try {
            $this->authorize('isCompany');
            $user = User::where('id', Auth::user()->id)->first();

            $logo_file = $request->file('logo');
            $user_controller = new UserController();
            $logo = $user_controller->storeImage($logo_file);

            Company::create([
                'user_id' => $user->id,
                'company_name' => $request->company_name,
                'logo' => $logo,
                'location' => $request->location,
                'about' => $request->about,
                'contact_info' => $request->contact_info
            ]);
            return $this->apiResponse(null, 'success',  201);
        } catch (AuthorizationException $authExp) {
            return $this->apiResponse(null, $authExp->getMessage(), 401);
        } catch (\Exception $ex) {
            return $this->apiResponse(null, $ex->getMessage(), $ex->getCode());
        }
    }

    public function update(Request $request) {
        try {
            $this->authorize('isCompany');
            $user = User::where('id', Auth::user()->id)->first();

            $logo_file = $request->file('logo');
            $user_controller = new UserController();
            $logo = $user_controller->storeImage($logo_file);

            Company::where('user_id', $user->id)->update([
                'company_name' => $request->company_name,
                'logo' => $logo,
                'location' => $request->location,
                'about' => $request->about,
                'contact_info' => $request->contact_info
            ]);
            return $this->apiResponse(null, 'success',  201);
        } catch (AuthorizationException $authExp) {
            return $this->apiResponse(null, $authExp->getMessage(), 401);
        } catch (\Exception $ex) {
            return $this->apiResponse(null, $ex->getMessage(), $ex->getCode());
        }
    }
}
