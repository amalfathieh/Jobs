<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Save extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'opportunity_id'
    ];

    public function oppourtunities() {
        return $this->belongsTo(Opportunity::class);
    }
}
