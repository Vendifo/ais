<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Discipline;
use App\Models\Department;

class DisciplineSeeder extends Seeder
{
    public function run()
    {
        $mathDept = Department::where('name', 'Кафедра математики')->first();
        $physDept = Department::where('name', 'Кафедра физики')->first();
        $csDept = Department::where('name', 'Кафедра информатики')->first();

        Discipline::insert([
            ['name' => 'Математика', 'department_id' => $mathDept->id],
            ['name' => 'Физика', 'department_id' => $physDept->id],
            ['name' => 'Информатика', 'department_id' => $csDept->id],
        ]);
    }
}
