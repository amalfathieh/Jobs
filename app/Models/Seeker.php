<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seeker extends Model
{
    use HasFactory;
    protected $guarded=[];

    public static function boot()
    {
        parent::boot();
        // Validate uniqueness of user_id when creating a new company
        static::creating(function ($seeker) {
            if (self::where('user_id', $seeker->user_id)->exists()) {
                throw new \Exception('User already has a seeker job.');
            }
        });
    }
    public function user(){
        return $this->belongsTo(User::class);
    }
}
