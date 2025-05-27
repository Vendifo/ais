<?php
namespace App\Http\Controllers;

use App\Models\PlannedLoad;
use App\Models\ActualLoad;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class LoadComparisonController extends Controller
{
    public function index(Request $request) {
        $teacherId = $request->query('teacher_id');
        $semester = $request->query('semester');
        $year = $request->query('year');
    
        // Получаем PlannedLoad с опциональными фильтрами
        $plannedQuery = PlannedLoad::query();
        if ($teacherId) {
            $plannedQuery->where('teacher_id', $teacherId);
        }
        if ($semester) {
            $plannedQuery->where('semester', $semester);
        }
        if ($year) {
            $plannedQuery->where('year', $year);
        }
        $planned = $plannedQuery->get();
    
        // Получаем ActualLoad с опциональными фильтрами
        $actualQuery = ActualLoad::query();
        if ($teacherId) {
            $actualQuery->where('teacher_id', $teacherId);
        }
        if ($semester) {
            $actualQuery->where('semester', $semester);
        }
        if ($year) {
            $actualQuery->where('year', $year);
        }
        $actual = $actualQuery->get();
    
        $summary = [];
    
        foreach ($planned as $plan) {
            $fact = $actual->where('discipline_id', $plan->discipline_id)
                           ->where('type', $plan->type)
                           ->sum('hours');
    
            $summary[] = [
                'discipline' => $plan->discipline->name ?? 'Unknown',
                'type' => $plan->type,
                'planned_hours' => $plan->hours,
                'actual_hours' => $fact,
                'difference' => $fact - $plan->hours,
            ];
        }
    
        return response()->json($summary);
    }
    
}
