<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;
    protected $fillable =[
      'chat_id',
      'sender_id',
      'message',
      'seen'
    ];
    public function chat(){
        $this->belongsToMany(Chat::class);
    }
}
