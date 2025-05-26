<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $fillable = ['name', 'year', 'department_id'];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
