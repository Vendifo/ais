<?php

namespace App\Http\Controllers;

use App\Models\PlannedLoad;
use App\Models\ActualLoad;
use App\Models\User;
use App\Models\Discipline;
use App\Models\Department;
use Illuminate\Http\Request;
// use PDF; // Пока не используем
// use Maatwebsite\Excel\Facades\Excel; // Пока не используем

class ReportController extends Controller
{
    // Отчёт по преподавателям (сравнение нагрузки)
    public function workloadComparison(Request $request)
    {
        $semester = $request->input('semester');
        $year = $request->input('year');

        $teachers = User::whereHas('roles', fn($q) => $q->where('name', 'teacher'))->get();

        $report = [];

        foreach ($teachers as $teacher) {
            $planned = PlannedLoad::where('teacher_id', $teacher->id)
                ->when($semester, fn($q) => $q->where('semester', $semester))
                ->when($year, fn($q) => $q->where('year', $year))
                ->get();

            $actual = ActualLoad::where('teacher_id', $teacher->id)
                ->when($semester, fn($q) => $q->where('semester', $semester))
                ->when($year, fn($q) => $q->where('year', $year))
                ->get();

            $report[] = [
                'teacher' => $teacher->name,
                'planned_total_hours' => $planned->sum('hours'),
                'actual_total_hours' => $actual->sum('hours'),
                'difference' => $actual->sum('hours') - $planned->sum('hours'),
                'details' => $planned->map(function ($plan) use ($actual) {
                    $actualHours = $actual->where('discipline_id', $plan->discipline_id)
                        ->where('group_id', $plan->group_id)
                        ->where('type', $plan->type)
                        ->sum('hours');

                    return [
                        'discipline' => $plan->discipline->name ?? '—',
                        'group' => $plan->group->name ?? '—',
                        'type' => $plan->type,
                        'planned_hours' => $plan->hours,
                        'actual_hours' => $actualHours,
                        'diff' => $actualHours - $plan->hours,
                    ];
                })->values(),
            ];
        }

        return response()->json($report);
    }

    // Отчёт по дисциплинам — суммарная нагрузка по дисциплинам
    public function workloadByDiscipline(Request $request)
    {
        $semester = $request->input('semester');
        $year = $request->input('year');

        $disciplines = Discipline::with(['plannedLoads' => function($q) use ($semester, $year) {
            $q->when($semester, fn($q) => $q->where('semester', $semester))
              ->when($year, fn($q) => $q->where('year', $year));
        }, 'actualLoads' => function($q) use ($semester, $year) {
            $q->when($semester, fn($q) => $q->where('semester', $semester))
              ->when($year, fn($q) => $q->where('year', $year));
        }])->get();

        $report = $disciplines->map(function ($discipline) {
            $plannedHours = $discipline->plannedLoads->sum('hours');
            $actualHours = $discipline->actualLoads->sum('hours');

            return [
                'discipline' => $discipline->name,
                'planned_total_hours' => $plannedHours,
                'actual_total_hours' => $actualHours,
                'difference' => $actualHours - $plannedHours,
            ];
        });

        return response()->json($report);
    }

    // Отчёт по кафедрам — суммарная нагрузка по кафедрам
    public function workloadByDepartment(Request $request)
    {
        $semester = $request->input('semester');
        $year = $request->input('year');

        $departments = Department::with(['disciplines.plannedLoads' => function ($q) use ($semester, $year) {
            $q->when($semester, fn($q) => $q->where('semester', $semester))
              ->when($year, fn($q) => $q->where('year', $year));
        }, 'disciplines.actualLoads' => function ($q) use ($semester, $year) {
            $q->when($semester, fn($q) => $q->where('semester', $semester))
              ->when($year, fn($q) => $q->where('year', $year));
        }])->get();

        $report = $departments->map(function ($department) {
            $plannedHours = 0;
            $actualHours = 0;

            foreach ($department->disciplines as $discipline) {
                $plannedHours += $discipline->plannedLoads->sum('hours');
                $actualHours += $discipline->actualLoads->sum('hours');
            }

            return [
                'department' => $department->name,
                'planned_total_hours' => $plannedHours,
                'actual_total_hours' => $actualHours,
                'difference' => $actualHours - $plannedHours,
            ];
        });

        return response()->json($report);
    }

    // Отчёт по видам нагрузки — например, лекция, практика и т.п.
    public function workloadByType(Request $request)
    {
        $semester = $request->input('semester');
        $year = $request->input('year');

        $types = PlannedLoad::select('type')->distinct()->pluck('type');

        $report = [];

        foreach ($types as $type) {
            $plannedHours = PlannedLoad::where('type', $type)
                ->when($semester, fn($q) => $q->where('semester', $semester))
                ->when($year, fn($q) => $q->where('year', $year))
                ->sum('hours');

            $actualHours = ActualLoad::where('type', $type)
                ->when($semester, fn($q) => $q->where('semester', $semester))
                ->when($year, fn($q) => $q->where('year', $year))
                ->sum('hours');

            $report[] = [
                'type' => $type,
                'planned_total_hours' => $plannedHours,
                'actual_total_hours' => $actualHours,
                'difference' => $actualHours - $plannedHours,
            ];
        }

        return response()->json($report);
    }

    // Заготовка экспорта PDF (пока не реализовано)
    public function exportPdf(Request $request)
    {
        return response()->json(['message' => 'Экспорт в PDF пока не реализован'], 501);
    }

    // Заготовка экспорта Excel (пока не реализовано)
    public function exportExcel(Request $request)
    {
        return response()->json(['message' => 'Экспорт в Excel пока не реализован'], 501);
    }
}
