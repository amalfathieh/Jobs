<?php

namespace App\Http\Controllers;

use App\Http\Requests\OpportunityRequest;
use App\Models\Opportunity;
use App\Traits\responseTrait;
use Illuminate\Http\Request;

class OpportunityController extends Controller
{
    use responseTrait;
    public function addOpportunity(OpportunityRequest $request) {
        try {

        } catch (\Exception $ex) {
            return $this->apiResponse(null, $ex->getMessage(), $ex->getCode());
        }
    }

    public function allOpportunities() {
        $opportunities = Opportunity::all();
        return $this->apiResponse($opportunities, null, 200);
    }
}
