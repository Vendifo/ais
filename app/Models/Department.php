<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Department extends Model
{

    use HasFactory; 
    protected $fillable = ['name', 'description'];

    public function disciplines()
    {
        return $this->hasMany(Discipline::class);
    }

    public function groups()
    {
        return $this->hasMany(Group::class);
    }

    public function teachers()
    {
        return $this->hasMany(Teacher::class);
    }
    
}
