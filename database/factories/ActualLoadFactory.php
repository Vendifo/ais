<?php

namespace Database\Factories;

use App\Models\ActualLoad;
use App\Models\PlannedLoad;
use App\Models\User;
use App\Models\Discipline;
use App\Models\Group;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActualLoadFactory extends Factory
{
    protected $model = ActualLoad::class;

    public function definition()
    {
        $teacher = User::whereHas('roles', fn($q) => $q->where('name', 'teacher'))->inRandomOrder()->first();
        $discipline = Discipline::inRandomOrder()->first();
        $group = Group::inRandomOrder()->first();

        // Опционально найдём связанную плановую нагрузку (может быть null)
        $plannedLoad = PlannedLoad::where('teacher_id', $teacher?->id)
            ->where('discipline_id', $discipline?->id)
            ->where('group_id', $group?->id)
            ->inRandomOrder()
            ->first();

        return [
            'planned_load_id' => $plannedLoad?->id,
            'teacher_id' => $teacher?->id,
            'discipline_id' => $discipline?->id,
            'group_id' => $group?->id,
            'type' => $this->faker->randomElement(['лекция', 'практика', 'лабораторная']),
            'hours' => $this->faker->numberBetween(5, 50),
            'semester' => $this->faker->randomElement(['Весна', 'Осень']),
            'year' => $this->faker->year(),
            'comment' => $this->faker->sentence(),
        ];
    }
}
