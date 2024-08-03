<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
class News extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'title',
        'body',
        'file',
        'created_by'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['*'])
        ->useLogName('News');
    }

    public function user() {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
