<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Apply extends Model
{
    use HasFactory;
    protected $fillable = [
        "seeker_id",
        "opportunity_id",
        "status"
    ];

    public function seeker(){
        return $this->belongsTo(Seeker::class);
    }

    public function opportunity() {
        return $this->belongsTo(Opportunity::class);
    }
}
