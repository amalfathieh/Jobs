<?php

namespace App\Models;

use Spatie\Permission\Traits\HasRoles;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'user_name',
        'email',
        'password',
        'role',
        'is_verified',
        'fcm_token'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function seeker()
    {
        return $this->hasOne(Seeker::class);
    }
    public function company()
    {
        return $this->hasOne(Company::class);
    }

    public function chats(){
        return $this->belongsToMany(Chat::class,'chat_user_pivot','user_id','chat_id');
    }
    public function employee()
    {
        return $this->hasOne(Employee::class);
    }

    public function followers(){
        return $this->belongsToMany(User::class,'followers' , 'follower_id','followee_id')->withTimestamps();
    }
    public function followings(){
        return $this->belongsToMany(User::class,'followers' , 'followee_id' , 'follower_id')->withTimestamps();
    }
}
