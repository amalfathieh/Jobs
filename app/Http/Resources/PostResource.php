<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
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
            'user_id' =>$this->seeker->user->id,
            'seeker_id' => $this->seeker_id,
            'created_by' => $this->seeker->first_name . ' ' . $this->seeker->last_name,
            'profile_img' => $this->seeker->image,
            'body' => $this->body,
            'file' => $this->file,
<<<<<<< HEAD
            'created_at' => $this->created_at->format('d-M-Y'),
            'updated_at' => $this->updated_at->format('d-M-Y'),
            'created_at_with_time' => $this->created_at->format('M-d-Y h:m A'),
            'updated_at_with_time' => $this->updated_at->format('d-M-Y')
=======
            'type' => $this->type,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
>>>>>>> 42cf080c5755eb3df5b0b135efbb4f7ecbc008c1
        ];
    }
}
