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
        return [
            'created_by' => $this->created_by,
            'user_id' => $this->user_id,
            'reason_id' => $this->reason_id,
            'reason_name' => $this->reason->title,
            'another_reason' => $this->another_reason,
        ];
    }
}
