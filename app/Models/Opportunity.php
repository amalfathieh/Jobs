<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Opportunity extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'title',
        'body',
        'file',
        'location',
        'job_type',
        'work_place_type',
        'job_hours',
        'qualifications',
        'skills_req',
        'salary',
        'vacant'
    ];

    protected $casts = [
        'qualifications' => 'array',
        'skills_req' => 'array'
    ];

    public function company() {
        return $this->belongsTo(Company::class);
    }
}
