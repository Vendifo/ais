<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        // Получаем случайную кафедру (или null)
        $departmentId = Department::inRandomOrder()->value('id');

        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => Hash::make('password'), // простой пароль для теста
            'api_token' => Str::random(60),
            'department_id' => $departmentId,
        ];
    }

    // Состояние для администратора
    public function admin()
    {
        return $this->state(fn() => [
            'name' => 'Admin ' . $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
        ]);
    }

    // Состояние для методиста
    public function methodist()
    {
        return $this->state(fn() => [
            'name' => 'Methodist ' . $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
        ]);
    }

    // Состояние для преподавателя с кафедрой
    public function teacher()
    {
        return $this->state(fn() => [
            'name' => 'Teacher ' . $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'department_id' => Department::inRandomOrder()->value('id'),
        ]);
    }
}
