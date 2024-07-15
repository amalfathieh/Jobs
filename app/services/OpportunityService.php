<?php


namespace App\services;


use App\Models\Opportunity;
use App\Traits\responseTrait;
use Illuminate\Support\Facades\Auth;

class OpportunityService
{
    use responseTrait;
    protected $fileService;
    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }
    public function createOpportunity(
        $company_id, $title, $body, $file,
        $location, $job_type, $work_place_type, $job_hours,
        $qualifications, $skills_req, $salary, $vacant
    ){

        $file = $this->fileService->store($file, 'opportunity');
        $opportunity = Opportunity::create([
            'company_id' => $company_id,
            'title' => $title,
            'body' => $body,
            'file' => $file,
            'location' => $location,
            'job_type' => $job_type,
            'work_place_type' => $work_place_type,
            'job_hours' => $job_hours,
            'qualifications' => $qualifications,
            'skills_req' => $skills_req,
            'salary' => $salary,
            'vacant' => $vacant
        ]);
        return $opportunity ;
    }

    public function update($request, $opportunity_id){
        $opportunity_file = null;
        $id = Auth::user()->company->id;
        $opportunity = Opportunity::where('company_id', $id)->where('id', $opportunity_id)->first();
        if($opportunity){
            $old_file = $opportunity['file'];
            if ($request->hasFile('file') && $request->file != '') {
                    $opportunity_file = $this->fileService->update($request->file, $old_file ,'opportunity');
            }
            $qualifications = json_decode($request->qualifications);
            $skills_req = json_decode($request->skills_req);
                $opportunity->update([
                    'title' => $request->title ?? $opportunity['title'],
                    'body' => $request['body'] ?? $opportunity['body'],
                    'file' => $opportunity_file,
                    'location' => $request['location'] ?? $opportunity['location'],
                    'job_type' => $request['job_type'] ?? $opportunity['job_type'],
                    'work_place_type' => $request['work_place_type'] ?? $opportunity['work_place_type'],
                    'job_hours' => $request['job_hours'] ?? $opportunity['job_hours'],
                    'qualifications' => $qualifications ?? $opportunity['qualifications'],
                    'skills_req' => $skills_req ?? $opportunity['skills_req'],
                    'salary' => $request['salary'] ?? $opportunity['salary'],
                    'vacant' =>$request['vacant'] ?? $opportunity['vacant']
                ]);
            return $this->apiResponse($opportunity, __('strings.updated_successfully'), 201);
        }
        return $this->apiResponse(null , __('strings.not_found') ,404);
    }

}
