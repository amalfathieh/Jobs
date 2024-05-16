<?php

namespace App\Http\Controllers;

use App\Http\Requests\OpportunityRequest;
use App\Models\Opportunity;
use App\Traits\responseTrait;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use App\services\OpportunityService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;

class OpportunityController extends Controller
{
    use responseTrait;
    public function addOpportunity(OpportunityRequest $request, OpportunityService $service) {
        try {
            $this->authorize('isCompany');
            $file = $request->file('file');
            $company_id = Auth::user()->company->id;

            $service->createOpportunity(
                $company_id, $request->title, $request->body,
                $file, $request->location, $request->job_type,
$request->work_place_type, $request->job_hours, $request->qualifications,
    $request->skills_req, $request->salary, $request->vacant
            );
            return $this->apiResponse(null, 'Opportunity added successfully', 201);
        }catch (AuthenticationException $authExp){
            return $this->apiResponse(null, $authExp->getMessage(), 401);
        }
        catch (\Exception $ex) {
            return $this->apiResponse(null, $ex->getMessage(), $ex->getCode());
        }
    }

    public function updateOpportunity(Request $request, OpportunityService $opportunityService, $id){
        try {
            $this->authorize('isCompany');
            $opportunityService->update($request, $id);
            return $this->apiResponse(null, 'Opportunity updated successfully', 201);

        }catch (AuthorizationException $authExp) {
            return $this->apiResponse(null, $authExp->getMessage(), 401);
        }catch (\Exception $ex) {
                return $this->apiResponse(null, $ex->getMessage(), 500);
        }
    }

    public function allOpportunities() {
        $opportunities = Opportunity::all();
        return $this->apiResponse($opportunities, null, 200);
    }
}
