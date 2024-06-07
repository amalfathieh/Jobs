<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OpportunityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' =>$this->company->user_id,
            'company_id' => $this->company_id,
            'company_name' => $this->company->company_name,
            'title' => $this->title,
            'body' => $this->body,
            'file' => $this->file,
            'location' => $this->location,
            'job_type' => $this->job_type,
            'work_place_type' => $this->work_place_type,
            'job_hours' => $this->job_hours,
            'qualifications' => $this->qualifications,
            'skills_req' => $this->skills_req,
            'salary' => $this->salary,
            'vacant' => $this->vacant,
            'craeted_at' => $this->created_at
        ];
    }
}
