<?php

namespace App\Http\Resources;

use App\Models\Company;
use App\Models\Seeker;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'user_name' => $this->user_name,
            'email' => $this->email,
            'is_verified' => $this->is_verified === 1,
            'role' => $this->role,
        ];

        if ($this->role == 'job_seeker') {
            $seeker = Seeker::where('user_id', $this->id)->first();

            $data['more_info'] = new SeekerResource($seeker);
        } else {
            $company = Company::where('user_id', $this->id)->first();

            $data['more_info'] = new CompanyResource($company);
        }

        return $data;
    }
}
