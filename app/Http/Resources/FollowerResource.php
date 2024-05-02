<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FollowerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $result = [
            'id' => $this->id,
            'role' => $this->role,
        ];

        if ($this->role == 'company') {
            $result['name'] = $this->company->company_name;
            $result['image'] = $this->company->logo;
        } else {
            $result['name'] = $this->seeker->first_name . ' ' . $this->seeker->last_name;
            $result['image'] = $this->seeker->image;
        }

        return $result;
    }
}
