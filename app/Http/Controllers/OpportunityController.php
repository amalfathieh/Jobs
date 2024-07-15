<?php

namespace App\Http\Controllers;

use App\Http\Requests\OpportunityRequest;
use App\Http\Resources\OpportunityResource;
use App\Models\Company;
use App\Models\Opportunity;
use App\Models\User;
use App\Notifications\SendNotification;
use App\Traits\responseTrait;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use App\services\OpportunityService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;

use function PHPUnit\Framework\isEmpty;

class OpportunityController extends Controller
{
    use responseTrait;
    public function addOpportunity(OpportunityRequest $request, OpportunityService $service) {
        try {
            $file = $request->file('file');
            $user = User::find(Auth::user()->id);
            $company_id =$user->company->id;
            $location = $user->company->location;
            $qualifications = json_decode($request->qualifications);
            $skills_req = json_decode($request->skills_req);
            $opportunity = $service->createOpportunity(
                $company_id, $request->title, $request->body,
                $file, $location, $request->job_type,
                $request->work_place_type, $request->job_hours, $qualifications,
                $skills_req, $request->salary, $request->vacant
            );
            // get followers tokens
            $followers = $user->followers;
            if ($followers) {
                $tokens = [];
                foreach($followers as $follower){
                    $tokens = array_merge($tokens , $follower->routeNotificationForFcm());
                }
                $data =[
                    'obj_id'=>$opportunity->id,
                    'title'=>'Job Opportunity',
                    'body'=>$user->company->company_name.' has just posted a new job opportunity: '.$request->title.' Apply now!',
                ];

                Notification::send($followers,new SendNotification($data));
//                $this->sendPushNotification($data['title'],$data['body'],$tokens);
            }
            return $this->apiResponse($opportunity, 'Opportunity added successfully', 201);
        }catch (\Exception $ex) {
            return $this->apiResponse(null, $ex->getMessage(), 500);
        }
    }

    public function updateOpportunity(Request $request,OpportunityService $opportunityService, $id){
        try {
            return $opp = $opportunityService->update($request, $id);
        }catch (\Exception $ex) {
                return $this->apiResponse(null, $ex->getMessage(), 500);
        }
    }

    public function delete($id){
        $opportunity = Opportunity::find($id);
        $user = User::where('id', Auth::user()->id)->first();
        if ($opportunity) {
            if (($user->hasRole('company') && $opportunity['company_id'] == $user->company->id) || (($user->hasRole('employee') || $user->hasRole('owner')) && $user->can('opportunity delete'))) {
                $opportunity->delete();
                return $this->apiResponse(null, __('strings.deleted_successfully'), 200);
            }
            return $this->apiResponse(null,__('strings.authorization_required'),403);
        }
        return $this->apiResponse(null, __('strings.not_found'), 404);
    }

    public function getMyOpportunities() {
        $user = User::where('id', Auth::user()->id)->first();
        $company = Company::where('id', $user->company->id)->first();
        $opportunities = OpportunityResource::collection(OpportunityResource::collection($company->opportunities));
        return $this->apiResponse($opportunities, 'These are all my opportunites', 200);
    }

    public function allOpportunities() {
        $userId = Auth::user()->id;
        $opportunities = Opportunity::select('opportunities.*')->addSelect(DB::raw("EXISTS(SELECT 1 FROM followers WHERE followers.follower_id = opportunities.company_id AND followers.followee_id = $userId) AS is_followed"))
            ->orderByDesc('is_followed')
            ->latest()
            ->get();

        $opportunities = OpportunityResource::collection($opportunities);
        return $this->apiResponse($opportunities, 'successfully', 200);
    }

    public function getAllOpp() {
        $opportunities = Opportunity::all();
        $groupedOpportunities = [];
        $chunkSize = 3;

        foreach ($opportunities as $index => $opportunity) {
            $groupIndex = (int) ($index / $chunkSize);
            $groupedOpportunities[$groupIndex][] = $opportunity;
        }
        $data = [];
        foreach ($groupedOpportunities as $group) {
            $data[] = OpportunityResource::collection($group);
        }
        return $data;
    }
    //الفرص المقترحة
    public function proposed_Jobs(){
        $seeker = User::find(Auth::user()->id)->seeker;
        $companies = Company::where('domain', $seeker->specialization)->get();

        $opportunities = [];
        foreach($companies as $company){
            $companyOpportunities = $company->opportunities;
            foreach($companyOpportunities as $opportunity){
                $opportunities[] = new OpportunityResource($opportunity);
            }
        }
        return $this->apiResponse($opportunities , 'proposed Jobs' ,200);
    }
}
