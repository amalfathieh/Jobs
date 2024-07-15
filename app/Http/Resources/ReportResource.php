<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $type = array_search('company', $this->user1->roles_name) ? 'company' : 'seeker';
        return [
            'id' => $this->id,
            'created_by' => $this->user1->user_name,
            'created_by_image' => $type == 'company' ? $this->user1->company->logo : $this->user1->seeker->image,
            'user_name' => $this->user2->user_name,
            'reason_id' => $this->reason_id,
            'reason_name' => $this->reason->title,
            'another_reason' => $this->another_reason,
            'notes' => $this->notes,
            'created_at' => $this->created_at->format('M-d-Y h:i A'),
            'is_reviewed' => $this->is_reviewed,
        ];
    }
}
