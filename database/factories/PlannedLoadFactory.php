<?php

namespace Database\Factories;

use App\Models\PlannedLoad;
use App\Models\User;
use App\Models\Discipline;
use App\Models\Group;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlannedLoadFactory extends Factory
{
    protected $model = PlannedLoad::class;

    public function definition()
    {
        // Выбираем случайного учителя с ролью teacher
        $teacher = User::whereHas('roles', fn($q) => $q->where('name', 'teacher'))->inRandomOrder()->first();
        // Берём случайную дисциплину и группу
        $discipline = Discipline::inRandomOrder()->first();
        $group = Group::inRandomOrder()->first();

        return [
            'teacher_id' => $teacher?->id,
            'discipline_id' => $discipline?->id,
            'group_id' => $group?->id,
            'department_id' => $teacher?->department_id,  // <--- добавь сюда
            'type' => $this->faker->randomElement(['лекция', 'практика', 'лабораторная']),
            'hours' => $this->faker->numberBetween(10, 50),
            'semester' => $this->faker->randomElement(['Весна', 'Осень']),
            'year' => $this->faker->year(),
            'created_by' => $teacher?->id,
        ];
        
    }
}
