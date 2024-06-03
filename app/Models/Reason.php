<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reason extends Model
{
    use HasFactory;

    protected $fillable = [
        'title'
    ];

    public function reports() {
        return $this->hasMany(Report::class, 'id', 'reason_id');
    }

}
