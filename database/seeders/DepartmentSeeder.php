<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    public function run()
    {
        $departments = [
            ['name' => 'Кафедра информатики'],
            ['name' => 'Кафедра математики'],
            ['name' => 'Кафедра физики'],
            ['name' => 'Кафедра иностранных языков'],
        ];

        foreach ($departments as $department) {
            Department::updateOrCreate(['name' => $department['name']], $department);
        }
    }
}
