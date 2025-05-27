<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Discipline extends Model
{
    protected $fillable = ['name', 'description', 'department_id'];
    // Отношение: у дисциплины много плановых нагрузок
    public function plannedLoads()
    {
        return $this->hasMany(PlannedLoad::class, 'discipline_id');
    }

    // Отношение: у дисциплины много фактических нагрузок
    public function actualLoads()
    {
        return $this->hasMany(ActualLoad::class, 'discipline_id');
    }
}
