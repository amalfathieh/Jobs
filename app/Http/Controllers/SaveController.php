<?php

namespace App\Http\Controllers;

use App\Http\Resources\OpportunityResource;
use App\Models\Opportunity;
use App\Traits\responseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Models\Activity;

class SaveController extends Controller
{
    //
    use responseTrait;
    public function saveOpportunity($opportunityId)
    {
        $user = Auth::user();
        $opportunity = Opportunity::find($opportunityId);

        if($opportunity) {
            if ($user->savedOpportunities()->where('opportunity_id', $opportunityId)->exists()) {
                $user->savedOpportunities()->detach($opportunity);
                $message = 'Opportunity delete from saved items.';
            }
            else {
                $user->savedOpportunities()->attach($opportunity);
                $message = 'Opportunity added to saved items';
            }
            return $this->apiResponse(null,$message,200);
        }
            return $this->apiResponse(null,'Opportunity not found.',404);
    }
    public function getSavedItems(){
        $user = Auth::user();
        $savedItems = $user->savedOpportunities;
        $data = OpportunityResource::collection($savedItems);
        return $this->apiResponse($savedItems , 'success' , 200);
    }
}
