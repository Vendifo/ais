<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\User;
use App\Models\Discipline;
use App\Models\Group;
use App\Models\Department;

class PlannedLoad extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id', 'discipline_id', 'group_id',
        'type', 'hours', 'semester', 'year', 'created_by', 'department_id'
    ];

    public function teacher() {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function discipline() {
        return $this->belongsTo(Discipline::class);
    }

    public function group() {
        return $this->belongsTo(Group::class);
    }

    public function creator() {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function department() {
        return $this->belongsTo(Department::class);
    }
}
