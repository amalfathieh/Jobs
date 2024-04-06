<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompanyRequest;
use App\Models\Company;
use App\Models\User;
use App\services\FileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\AuthorizationException;

class CompanyController extends Controller
{
    use responseTrait;
    public function createCompany(CompanyRequest $request, FileService $fileService)
    {
        try {
            $this->authorize('isCompany');
            $user = User::where('id', Auth::user()->id)->first();

            $logo_file = $request->file('logo');
            $logo = $fileService->store($logo_file,'company');
            Company::create([
                'user_id' => $user->id,
                'company_name' => $request->company_name,
                'location' => $request->location,
                'about' => $request->about,
                'contact_info' => $request->contact_info
            ]);
            Company::where('user_id', $user->id)->update(['logo' => $logo]);
            return $this->apiResponse(null, 'success',  201);
        } catch (AuthorizationException $authExp) {
            return $this->apiResponse(null, $authExp->getMessage(), 401);
        } catch (\Exception $ex) {
            return $this->apiResponse(null, $ex->getMessage(), $ex->getCode());
        }
    }

    public function update(Request $request , FileService $fileService) {
        try {
            $this->authorize('isCompany');
            $user = User::where('id', Auth::user()->id)->first();

            $logo_file = $request->file('logo');
            $company = $user->company;
            $old_file = $company['logo'];
            if ($request->hasFile('logo') && $logo_file != '') {
                $logo = $fileService->update($logo_file, $old_file, 'company');
            }
            $company->update([
                'company_name' => $request->company_name ?? $company['company_name'],
                'logo' => $logo ?? $company['logo'],
                'location' => $request->location ?? $company['location'],
                'about' => $request->about ?? $company['about'],
                'contact_info' => $request->contact_info ?? $company['contact_info']
            ]);
            return $this->apiResponse(null, 'success',  201);
        } catch (AuthorizationException $authExp) {
            return $this->apiResponse(null, $authExp->getMessage(), 401);
        } catch (\Exception $ex) {
            return $this->apiResponse(null, $ex->getMessage(), $ex->getCode());
        }
    }
}
