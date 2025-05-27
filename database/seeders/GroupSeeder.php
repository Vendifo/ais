<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Group;
use App\Models\Department;

class GroupSeeder extends Seeder
{
    public function run()
    {
        $department = Department::where('name', 'Кафедра информатики')->first();

        Group::insert([
            ['name' => 'ПИ-21', 'department_id' => $department->id, 'year' => 2021],
            ['name' => 'ПИ-22', 'department_id' => $department->id, 'year' => 2022],
        ]);
    }
}
