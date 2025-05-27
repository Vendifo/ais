<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PlannedLoad;
use App\Models\User;
use App\Models\Discipline;
use App\Models\Group;

class PlannedLoadSeeder extends Seeder
{
    public function run()
    {
        // Найдём пользователя с ролью teacher
        $teacher = User::whereHas('roles', function ($query) {
            $query->where('name', 'teacher');
        })->first();

        // Возьмём первую дисциплину и группу для связи
        $discipline = Discipline::first();
        $group = Group::first();

        // Проверим, что данные существуют
        if (!$teacher || !$discipline || !$group) {
            $this->command->error('Не найдены необходимые данные для сидера PlannedLoad');
            return;
        }

        PlannedLoad::create([
            'teacher_id' => $teacher->id,
            'discipline_id' => $discipline->id,
            'group_id' => $group->id,
            'type' => 'лекция',
            'hours' => 30,
            'semester' => 'Весна',
            'year' => 2025,
            'created_by' => $teacher->id,
        ]);
    }
}
