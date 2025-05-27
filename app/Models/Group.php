<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Group extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'year', 'department_id'];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
