<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobTitle extends Model
{
    use HasFactory;

    protected $fillable = [
        'title'
    ];

    public function employees() {
        return $this->belongsToMany(Employee::class);
    }

    public function permissions(){
        return $this->belongsToMany(Permission::class);
    }
}
