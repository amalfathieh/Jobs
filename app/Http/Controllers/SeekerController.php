<?php

namespace App\Http\Controllers;

use App\Http\Requests\SeekerRequest;
use App\services\SeekerService;
use Illuminate\Http\Request;

class SeekerController extends Controller
{
    use responseTrait;
    // CREATE PROFILE FOR J0B_SEEKER
    public function profile(SeekerRequest $request,SeekerService $service){
       $image = $request->file('image');
        $job_seeker = $service->createSeeker(
            $request->first_name,
            $request->last_name,
            $request->birth_day,
            $request->location,
            $image,
            $request->skills,
            $request->certificates,
            $request->about
        );
        return $this->apiResponse($job_seeker,'job_seeker created successfully',200);
    }
}
