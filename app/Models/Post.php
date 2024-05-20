<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    protected $fillable = [
        'seeker_id',
        'title',
        'body',
        'file'
    ];

    public function seeker(){
        return $this->belongsTo(Seeker::class);
    }

}
