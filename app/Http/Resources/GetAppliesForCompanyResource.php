<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GetAppliesForCompanyResource extends JsonResource
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
            'opportunity_name' => $this->opportunity->title,
            'seeker_id' => $this->user->id,
            'seeker_name' => $this->user->seeker->first_name . ' ' . $this->user->seeker->last_name,
            'seeker_email' => $this->user->email,
            'status' => $this->status,
            'created_at' => $this->created_at->format('M-d-Y h:m A'),
            'updated_at' => $this->updated_at->format('M-d-Y h:m A'),
        ];
    }
}
