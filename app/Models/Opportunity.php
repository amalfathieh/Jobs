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
        'work-place_type',
        'job_hours',
        'qualifications',
        'skills_req',
        'salary'
    ];

    public function company() {
        return $this->belongsTo(Company::class);
    }
}
