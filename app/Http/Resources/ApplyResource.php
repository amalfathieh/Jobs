<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplyResource extends JsonResource
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
            'company_name' => $this->opportunity->company->company_name,
            'status' => $this->status,
            'created_at' => $this->created_at
        ];
    }
}
