<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            DepartmentSeeder::class,   // если есть
            DisciplineSeeder::class,   // если есть
            GroupSeeder::class,        // если есть
            PlannedLoadSeeder::class,
            ActualLoadSeeder::class,
        ]);
    }
}
