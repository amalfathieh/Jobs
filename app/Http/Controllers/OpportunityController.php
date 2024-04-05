<?php

namespace App\Http\Controllers;

use App\Http\Requests\OpportunityRequest;
use Illuminate\Http\Request;

class OpportunityController extends Controller
{
    public function addOpportunity(OpportunityRequest $request) {
        try {

        } catch (\Exception $ex) {
            return $this->apiResponse(null, $ex->getMessage(), $ex->getCode());
        }
    }
}
