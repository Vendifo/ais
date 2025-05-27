<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Discipline;
use App\Models\Department;

class DisciplineSeeder extends Seeder
{
    public function run()
    {
        Discipline::truncate();

        if (Department::count() === 0) {
            $this->command->error('Нет кафедр! Запустите сначала DepartmentSeeder.');
            return;
        }

        // Создаём фиксированные дисциплины через фабрику с нужными состояниями
        Discipline::factory()->math()->create(['name' => 'Математика']);
        Discipline::factory()->physics()->create(['name' => 'Физика']);
        Discipline::factory()->cs()->create(['name' => 'Информатика']);

        // А затем ещё 20 рандомных
        Discipline::factory()->count(20)->create();
    }
}
