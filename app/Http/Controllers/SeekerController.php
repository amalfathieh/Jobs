<?php

namespace App\Http\Controllers;

use App\Http\Requests\SeekerRequest;
use App\Models\Seeker;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\User;
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
            //$this->authorize('isJobSeeker');
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

    public function update(Request $request ,SeekerService $seekerService){
        try {
            $this->authorize('isJobSeeker');
            $seekerService->update($request);
            return $this->apiResponse(null, 'profile updated successfully', 201);

        }catch (AuthorizationException $authExp) {
            return $this->apiResponse(null, $authExp->getMessage(), 401);
        }catch (\Exception $ex) {
                return $this->apiResponse(null, $ex->getMessage(), 500);
        }
    }

    public function createCV(Request $request) {
        $user = User::where('id', Auth::user()->id)->first();
        $info = Seeker::where('user_id', $user->id)->first();
        $data = $request->all();
        $pdf = Pdf::loadView('pdf.pdf', compact('data'));
        return $pdf->download('cv.pdf');
    }
}
