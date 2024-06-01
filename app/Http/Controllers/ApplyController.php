<?php

namespace App\Http\Controllers;

use App\Models\Apply;
use App\Models\Company;
use App\Models\Opportunity;
use App\Models\User;
use App\Notifications\SendNotification;
use App\services\FileService;
use App\Traits\responseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;

class ApplyController extends Controller
{
    use responseTrait;
    public function apply(Request $request, $id, FileService $fileService) {
        try {
            $apply = null;
            $opportunity = Opportunity::where('id', $id)->first();
            $company_id= $opportunity->company_id;
            if (!$request->file('cv')) {
                $apply = Apply::create([
                    'opportunity_id' => $id,
                    'user_id' => Auth::user()->id,
                    'company_id' => $company_id,
                    'full_name' => $request->full_name,
                    'birth_day' => $request->birth_day,
                    'location' => $request->location,
                    'about' => $request->about,
                    'skills' => $request->skills,
                    'certificates' => $request->certificates,
                    'languages' => $request->languages,
                    'projects' => $request->projects,
                    'experiences' => $request->experiences,
                    'contacts' => $request->contacts,
                ]);
            }
            else {
                $cv = $request->file('cv');
                $cv_path = $fileService->store($cv, 'job_seeker/applies');
                $apply = Apply::create([
                    'opportunity_id' => $id,
                    'user_id' => Auth::user()->id,
                    'company_id' => $company_id,
                    'cv' => $cv_path
                ]);
            }

            if ($apply) {
                $user = Company::where('id',$company_id)->first()->user;
                $tokens = $user->routeNotificationForFcm();
                //$body = 'لديكم طلب توظيف جديد لفرصة العمل '.$opportunity->title.' يرجى مراجعة الطلب في أقرب وقت ممكن.';
                $body = 'You have a new job application for the '.$opportunity->title.' position. Please review the application at your earliest convenience.';
                $data =[
                    'obj_id'=>$apply->id,
                    'title'=>'Job Application',
                    'body'=> $body,
                ];
                Notification::send($user,new SendNotification($data));
                $this->sendPushNotification($data['title'],$data['body'],$tokens);
                return $this->apiResponse($apply, 'The request has been sent successfully', 201);
            }
            return $this->apiResponse(null, 'There is an error', 400);
        } catch (\Exception $th) {
            return $this->apiResponse(null, $th->getMessage(), 500);
        }
    }

    public function getMyApplies() {
        $applies = Apply::where('user_id', Auth::user()->id)->orderBy('status')->get();
        return $this->apiResponse($applies, "These are all applies", 200);
    }

    public function update(Request $request, $id, FileService $fileService) {
        $apply = Apply::where('id', $id)->first();
        $newData = null;
        if ($apply->user_id === Auth::user()->id) {
            if (!$request->file('cv') && $apply->cv) {
                $fileService->delete($apply->cv);
                $apply->update([
                    'cv' => null,
                    'full_name' => $request->full_name  ?? $apply['full_name'],
                    'birth_day' => $request->birth_day ?? $apply['birth_day'],
                    'location' => $request->location ?? $apply['location'],
                    'about' => $request->about ?? $apply['about'],
                    'skills' => $request->skills ?? $apply['skills'],
                    'certificates' => $request->certificates ?? $apply['certificates'],
                    'languages' => $request->languages ?? $apply['languages'],
                    'projects' => $request->projects ?? $apply['projects'],
                    'experiences' => $request->experiences ?? $apply['experiences'],
                    'contacts' => $request->contacts ?? $apply['contacts'],
                ]);
                $newData = Apply::where('id', $id)->first();
                $newData = collect($newData)->except('cv');
            }
            else
                {
                    $new_cv = $request->file('cv');
                    $cv_path = $fileService->update($new_cv,$apply->cv, 'job_seeker/applies');
                    $apply->update([
                        'cv' => $cv_path ?? $apply['cv'],
                        'full_name' => null,
                        'birth_day' => null,
                        'location' => null,
                        'about' => null,
                        'skills' => null,
                        'certificates' => null,
                        'languages' => null,
                        'projects' => null,
                        'experiences' => null,
                        'contacts' => null,
                    ]);
                    $newData = Apply::where('id', $id)->select(['id', 'opportunity_id', 'user_id', 'company_id', 'cv'])->get();
                }
            if ($apply) {
                return $this->apiResponse($newData, 'The request has been updated successfully', 201);
            }
            return $this->apiResponse(null, 'There is an error', 400);
        }
        return $this->apiResponse(null, 'You are not allowed to do this', 400);
    }

    public function delete($id) {
        try {
            $user = User::where('id', Auth::user()->id)->first();
        $apply = Apply::where('id', $id)->first();
        if ($apply->user_id === $user->id || $apply->company_id === $user->company->id) {
            if ($apply->status === 'waiting' || ($user->company ? $apply->company_id === $user->company->id : false)) {
                $apply->delete();
                return $this->apiResponse(null, 'deleted successfully', 200);
            }
            return $this->apiResponse(null, "You cannot delete it, because it is not in a waiting state", 400);
        }
        return $this->apiResponse(null, "You are not allowed to do this", 400);
    } catch (\Exception $th) {
            return $this->apiResponse(null, $th->getMessage(), 500);
        }
    }
    // For Companies

    public function updateStatus($id, Request $request) {
        $validate = Validator::make($request->all(), [
            'status' => 'required|in:accepted,waiting,rejected'
        ]);

        if ($validate->fails()){
            return $this->apiResponse(null, $validate->errors(), 400);
        }
        $apply = Apply::where('id', $id)->first();
        $user = User::where('id', Auth::user()->id)->first();

        //send notification to job_seeker
        if ($apply->company_id === $user->company->id) {
            $apply->update([
                'status' => $request->status
            ]);
            if($request->status == 'accepted'){
                $body = 'Congratulations! Your application for '.$apply->opportunity->title.' at '.$user->company->company_name;
            }
            else if($request->status == 'rejected'){
//                $body = 'نأسف لإبلاغكم بأن طلبكم لوظيفة '.$apply->opportunity->title.' قد تم رفضه. في '.$user->company->company_name;
                $body = 'We regret to inform you that your application for the '.$apply->opportunity->title.' position has been rejected, at '.$user->company->company_name;
            }
            $user = User::find($apply->user_id);
            $tokens = $user->routeNotificationForFcm();
            $data =[
                'obj_id'=>$apply->id,
                'title'=>'Job Application',
                'body'=> $body,                                                                                    dy,
            ];
            Notification::send($user,new SendNotification($data));
            $this->sendPushNotification($data['title'],$data['body'],$tokens);


            $data = Apply::where('id', $id)->first()->select(['id', 'opportunity_id', 'user_id', 'company_id', 'status'])->first();
            return $this->apiResponse($data, 'Updated successfully', 200);
        }
        return $this->apiResponse(null, 'You are not allowed to do this', 400);
    }

    public function getApplies() {
        $user = User::where('id', Auth::user()->id)->first();
        $company = Company::where('id', $user->company->id)->first();
        $applies = Apply::where('company_id', $company->id)->get();

        $with_cv = Apply::where('company_id', $company->id)->where('cv', '!=', null)->get();
        $without_cv = Apply::where('company_id', $company->id)->where('cv', null)->get();

        $with_cv = collect($with_cv)->select(['id', 'opportunity_id', 'user_id', 'company_id', 'cv']);
        $data = [
            'with_cv' => $with_cv,
            'without_cv' => $without_cv
        ];
        return $this->apiResponse($data, 'These are all applies', 200);
    }
}
