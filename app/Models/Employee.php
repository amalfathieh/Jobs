<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'first_name',
        'middle_name',
        'last_name',
        'phone',
        'image',
        'starting_date',
        'job_id'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function job() {
        return $this->hasOne(JobTitle::class, 'id', 'job_id');
    }
}
