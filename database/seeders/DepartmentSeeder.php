<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    public function run()
    {
        Department::truncate();

        // Создаём фиксированные кафедры через фабрику с состояниями
        Department::factory()->informatics()->create();
        Department::factory()->mathematics()->create();
        Department::factory()->physics()->create();

        // А затем 17 случайных кафедр (чтобы в итоге было 20)
        Department::factory()->count(17)->create();
    }
}
