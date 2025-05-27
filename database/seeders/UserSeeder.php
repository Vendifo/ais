<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Department;

class UserSeeder extends Seeder
{   
    public function run()
    {
        $adminRole = Role::where('name', 'admin')->first();
        $methodistRole = Role::where('name', 'methodist')->first();
        $teacherRole = Role::where('name', 'teacher')->first();

        $departments = Department::all();

        if ($departments->isEmpty()) {
            $this->command->error('Кафедры не найдены, добавьте кафедры перед созданием пользователей');
            return;
        }

        // Администраторы
        User::factory(3)
            ->admin()
            ->create()
            ->each(fn($user) => $user->roles()->attach($adminRole));

        // Методисты
        User::factory(5)
            ->methodist()
            ->create()
            ->each(fn($user) => $user->roles()->attach($methodistRole));

        // Преподаватели
        User::factory(10)
            ->teacher()
            ->create()
            ->each(fn($user) => $user->roles()->attach($teacherRole));
    }
}
