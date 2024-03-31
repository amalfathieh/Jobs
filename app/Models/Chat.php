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
        'user2_id',
        ];
    public function user(){
        return $this->belongsTo(User::class,'id');
    }
    public function messages(){
        return $this->hasMany(Message::class);
    }
}
