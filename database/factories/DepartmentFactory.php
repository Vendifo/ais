<?php

namespace Database\Factories;

use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

class DepartmentFactory extends Factory
{
    protected $model = Department::class;

    public function definition()
    {
        return [
            'name' => $this->faker->unique()->company(),
            'description' => $this->faker->sentence(),
        ];
    }

    // state для конкретной кафедры информатики
    public function informatics()
    {
        return $this->state([
            'name' => 'Кафедра информатики',
            'description' => 'Кафедра информатики занимается обучением и исследованиями в области IT и программирования.',
        ]);
    }

    public function mathematics()
    {
        return $this->state([
            'name' => 'Кафедра математики',
            'description' => 'Кафедра математики занимается обучением математических дисциплин и научными исследованиями.',
        ]);
    }

    public function physics()
    {
        return $this->state([
            'name' => 'Кафедра физики',
            'description' => 'Кафедра физики проводит обучение и исследования в области физики.',
        ]);
    }
}
