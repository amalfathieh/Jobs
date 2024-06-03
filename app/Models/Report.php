<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
class Report extends Model
{
    use HasFactory, LogsActivity;
    protected $fillable = [
        'created_by',
        'user_id',
        'reason_id',
        'another_reason',
        'notes'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['*']);
    }

    public function reason() {
        return $this->belongsTo(Reason::class, null, 'reason_id');
    }

    public function user1() {
        return $this->belongsTo(User::class, null, 'created_by');
    }

    public function user2() {
        return $this->belongsTo(User::class, null, 'user2_id');
    }

}
