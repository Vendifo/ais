<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Group;
use App\Models\Department;

class GroupSeeder extends Seeder
{
    public function run()
    {
        Group::truncate();

        if (Department::count() === 0) {
            $this->command->error('Нет кафедр в базе. Запустите DepartmentSeeder!');
            return;
        }

        // Создаём 20 групп, каждая с рандомной кафедрой из базы
        Group::factory()->count(20)->create();
    }
}
