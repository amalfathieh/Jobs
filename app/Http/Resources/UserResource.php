<?php

namespace App\Http\Resources;

use App\Models\Company;
use App\Models\Employee;
use App\Models\Seeker;
use App\Models\User;
//use http\Client\Curl\User;
use FontLib\Table\Type\name;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use function Symfony\Component\String\u;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = User::where('id' , $this->id)->first();

            $data=[
                'id' => $this->id,
                'user_name' => $this->user_name,
                'email' => $this->email,
                'is_verified' => $this->is_verified === 1,
                'created_at'=>$this->created_at->toDateTimeString(),
                'role' => $this->roles_name,
            ];


             if($user->hasRole('employee')){
                $employee=$user->employee;
                $data['more_info'] = new EmployeeResource($employee);
            }

            else if ( $user->hasRole('job_seeker') ) {
                $seeker = $user->seeker;

                $data['more_info'] = new SeekerResource($seeker);
            }

            else if($user->hasRole('company')) {
                $company = $user->company;

                $data['more_info'] = new CompanyResource($company);
            }


        return $data;
    }
}
