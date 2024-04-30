<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobTitle extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'permission_id'
    ];

    public function employees() {
        return $this->belongsToMany(Employee::class);
    }

    public function permissions(){
        return $this->hasMany(Permission::class, 'id', 'permission_id');
    }
}
