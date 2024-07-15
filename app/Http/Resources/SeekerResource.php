<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SeekerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'gender' => $this->gender,
            'birth_day' => $this->birth_day,
            'location' => $this->location,
            'image' => $this->image,
            'skills' => $this->skills,
            'certificates' => $this->certificates,
            'about' => $this->about,
            'specialization' => $this->specialization,
        ];
    }
}
