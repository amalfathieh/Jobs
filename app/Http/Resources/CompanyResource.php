<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'user_id'=> $this->user_id,
            'company_id' =>$this->id,
            'company_name' => $this->company_name,
            'logo' => $this->logo,
            'location' => $this->location,
            'about' => $this->about,
            'domain' =>$this->domain,
            'contact_info' => $this->contact_info,
        ];
    }
}
