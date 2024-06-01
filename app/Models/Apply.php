<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Apply extends Model
{
    use HasFactory, LogsActivity;
    protected $fillable = [
        'user_id',
        'opportunity_id',
        'company_id',
        'cv',
        'full_name',
        'birth_day',
        'location',
        'about',
        'skills',
        'certificates',
        'languages',
        'projects',
        'experiences',
        'contacts',
        'status'
    ];

    protected $casts = [
        'skills' => 'array',
        'certificates' => 'array',
        'languages' => 'array',
        'projects' => 'array',
        'experiences' => 'array',
        'contacts' => 'array',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['*']);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function opportunity() {
        return $this->belongsTo(Opportunity::class);
    }
}
