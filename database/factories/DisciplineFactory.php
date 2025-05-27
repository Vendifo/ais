<?php

namespace Database\Factories;

use App\Models\Discipline;
use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

class DisciplineFactory extends Factory
{
    protected $model = Discipline::class;

    public function definition()
    {
        return [
            'name' => $this->faker->unique()->word(),
            'department_id' => Department::inRandomOrder()->first()->id,
        ];
    }

    // state для кафедры математики
    public function math()
    {
        return $this->state(function () {
            $dept = Department::where('name', 'Кафедра математики')->first();
            return ['department_id' => $dept ? $dept->id : null];
        });
    }

    // state для кафедры физики
    public function physics()
    {
        return $this->state(function () {
            $dept = Department::where('name', 'Кафедра физики')->first();
            return ['department_id' => $dept ? $dept->id : null];
        });
    }

    // state для кафедры информатики
    public function cs()
    {
        return $this->state(function () {
            $dept = Department::where('name', 'Кафедра информатики')->first();
            return ['department_id' => $dept ? $dept->id : null];
        });
    }
}
