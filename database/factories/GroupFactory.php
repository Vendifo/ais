<?php

namespace Database\Factories;

use App\Models\Group;
use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

class GroupFactory extends Factory
{
    protected $model = Group::class;

    public function definition()
    {
        $department = Department::inRandomOrder()->first();

        return [
            'name' => $this->faker->unique()->bothify('ПИ-##'), // например, ПИ-21, ПИ-35 и т.п.
            'year' => $this->faker->numberBetween(2018, 2025),
            'department_id' => $department ? $department->id : null,
        ];
    }
}
