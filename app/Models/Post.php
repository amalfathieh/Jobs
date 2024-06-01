<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
class Post extends Model
{
    use HasFactory, LogsActivity;
    protected $fillable = [
        'seeker_id',
        'body',
        'file'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['*']);
    }

    public function seeker(){
        return $this->belongsTo(Seeker::class);
    }

}
