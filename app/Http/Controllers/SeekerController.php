<?php

namespace App\Http\Controllers;

use App\Http\Requests\SeekerRequest;
use App\Models\Seeker;
use App\services\SeekerService;
use Illuminate\Http\Request;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;

class SeekerController extends Controller
{
    use responseTrait;
    // CREATE PROFILE FOR J0B_SEEKER
    public function create(SeekerRequest $request, SeekerService $service)
    {
        try {
            $this->authorize('isJobSeeker');
            $image = $request->file('image');
            $service->createSeeker(
                $request->first_name,
                $request->last_name,
                $request->birth_day,
                $request->location,
                $image,
                $request->skills,
                $request->certificates,
                $request->about
            );
            return $this->apiResponse(null, 'job_seeker created successfully', 201);
        } catch (AuthorizationException $authExp) {
            return $this->apiResponse(null, $authExp->getMessage(), 401);
        } catch (\Exception $ex) {
            return $this->apiResponse(null, $ex->getMessage(), 500);
        }
    }

    public function update(SeekerRequest $request){
        try {
            $id = Auth::user()->id;
            $seeker = Seeker::where('user_id', $id)->first();
            $seeker->update($request->all());
            return $this->apiResponse(null, 'profile updated successfully', 201);
        }catch (\Exception $ex) {
            return $this->apiResponse(null, $ex->getMessage(), 500);
        }
    }
}
