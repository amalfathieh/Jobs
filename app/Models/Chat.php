<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;
    protected $fillable=[
        'id',
        'user1_id',
        'user2_id'
    ];
    public function user(){
        return $this->belongsToMany(User::class,'chat_user_pivot','chat_id','user_id');
    }
    public function messages(){
        return $this->hasMany(Message::class);
    }
    public function lastMessage(){
        return $this->hasOne(Message::class,'chat_id')->latest()->first()->message;
    }

    public function lastTimeMessage(){
        return $this->hasOne(Message::class,'chat_id')->latest()->first()->created_at->toDateTimeString();
    }

    public function users(){
        return $this->belongsToMany(User::class,'chat_user_pivot');
    }
}
