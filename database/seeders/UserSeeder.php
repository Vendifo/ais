<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run()
    {
        $adminRole = Role::where('name', 'admin')->first();
        $methodistRole = Role::where('name', 'methodist')->first();
        $teacherRole = Role::where('name', 'teacher')->first();

        // Создаём администраторов
        for ($i = 1; $i <= 3; $i++) {
            $user = User::create([
                'name' => "Admin User $i",
                'email' => "admin{$i}@example.com",
                'password' => bcrypt('password'),
                'api_token' => Str::random(60),
            ]);
            $user->roles()->attach($adminRole->id);
        }

        // Создаём методистов / заведующих кафедрой
        for ($i = 1; $i <= 3; $i++) {
            $user = User::create([
                'name' => "Methodist User $i",
                'email' => "methodist{$i}@example.com",
                'password' => bcrypt('password'),
                'api_token' => Str::random(60),
            ]);
            $user->roles()->attach($methodistRole->id);
        }

        // Получаем все кафедры
        $departments = Department::all();

        if ($departments->isEmpty()) {
            $this->command->error('Кафедры не найдены, добавьте кафедры перед созданием преподавателей');
            return;
        }

        // Создаём преподавателей и присваиваем кафедру циклично
        for ($i = 1; $i <= 3; $i++) {
            $department = $departments->get(($i - 1) % $departments->count());

            $user = User::create([
                'name' => "Teacher User $i",
                'email' => "teacher{$i}@example.com",
                'password' => bcrypt('password'),
                'api_token' => Str::random(60),
                'department_id' => $department->id,
            ]);
            $user->roles()->attach($teacherRole->id);
        }
    }
}
