<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seeker extends Model
{
    use HasFactory;
    protected $guarded=[];
//    protected $fillable = [
//        'first_name',
//        'last_name',
//        'birth_day',
//        'location',
//        'image',
//        'skills',
//        'certificates',
//        'about'
//    ];
    public function user(){
        return $this->belongsTo(User::class);
    }
}
