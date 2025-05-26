<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
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
