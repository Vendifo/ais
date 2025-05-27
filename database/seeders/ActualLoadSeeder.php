<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ActualLoad;
use App\Models\User;
use App\Models\Discipline;
use App\Models\Group;
use App\Models\PlannedLoad;

class ActualLoadSeeder extends Seeder
{
    public function run()
    {
        // Находим пользователя с ролью teacher
        $teacher = User::whereHas('roles', function ($query) {
            $query->where('name', 'teacher');
        })->first();

        $discipline = Discipline::first();
        $group = Group::first();

        if (!$teacher || !$discipline || !$group) {
            $this->command->error('Не найдены необходимые данные для сидера ActualLoad');
            return;
        }

        // Опционально ищем связанную плановую нагрузку, чтобы привязать
        $plannedLoad = PlannedLoad::where('teacher_id', $teacher->id)
            ->where('discipline_id', $discipline->id)
            ->where('group_id', $group->id)
            ->first();

        ActualLoad::create([
            'planned_load_id' => $plannedLoad ? $plannedLoad->id : null,
            'teacher_id' => $teacher->id,
            'discipline_id' => $discipline->id,
            'group_id' => $group->id,
            'type' => 'лекция',
            'hours' => 28,
            'semester' => 'Весна',
            'year' => 2025,
            'comment' => 'Фактическое количество часов',
        ]);
    }
}
