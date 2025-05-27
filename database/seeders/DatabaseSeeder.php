<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            RoleSeeder::class,         // роли нужны, чтобы юзеры могли к ним привязываться
            DepartmentSeeder::class,   // кафедры нужны для дисциплин
            DisciplineSeeder::class,   // дисциплины нужны для нагрузок
            GroupSeeder::class,        // группы нужны для нагрузок
            UserSeeder::class,         // пользователи должны существовать перед нагрузками
            PlannedLoadSeeder::class,  // создают плановую нагрузку, ссылаясь на вышеуказанные данные
            ActualLoadSeeder::class,   // создают фактическую нагрузку
        ]);
    }
    
}
