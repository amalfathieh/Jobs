<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat_user_pivot extends Model
{
    use HasFactory;
    protected $table = 'chat_user_pivot';
    protected $fillable=[
        'id',
        'chat_id',
        'user_id'
        ];
}
