<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActualLoad extends Model
{

    use HasFactory;
    protected $fillable = [
        'planned_load_id', 'teacher_id', 'discipline_id',
        'group_id', 'type', 'hours', 'semester', 'year', 'comment'
    ];

    public function plannedLoad() {
        return $this->belongsTo(PlannedLoad::class);
    }

    public function teacher() {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function discipline() {
        return $this->belongsTo(Discipline::class);
    }

    public function group() {
        return $this->belongsTo(Group::class);
    }
}
