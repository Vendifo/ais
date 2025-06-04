<?php

namespace App\Http\Controllers;

use App\Models\PlannedLoad;
use Illuminate\Http\Request;
use App\Rules\TeacherExists;

class PlannedLoadController extends Controller
{
    public function index()
    {
        return PlannedLoad::with([
            'teacher.department',
            'discipline',
            'group',
            'department',
        ])->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'teacher_id' => ['required', 'integer', new TeacherExists],
            'discipline_id' => 'required|exists:disciplines,id',
            'group_id' => 'required|exists:groups,id',
            'department_id' => 'required|exists:departments,id',
            'type' => 'required|string|max:255',
            'hours' => 'required|integer|min:1',
            'semester' => 'required|string|max:50',
            'year' => 'required|integer',
        ]);

        $data['created_by'] = auth()->id();

        $plannedLoad = PlannedLoad::create($data);

        return $plannedLoad->load([
            'teacher.department',
            'discipline',
            'group',
            'department',
        ]);
    }

    public function show($id)
    {
        $plannedLoad = PlannedLoad::with([
            'teacher.department',
            'discipline',
            'group',
            'department',
        ])->findOrFail($id);

        return response()->json($plannedLoad);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'teacher_id' => ['sometimes', 'integer', new TeacherExists],
            'discipline_id' => 'sometimes|integer|exists:disciplines,id',
            'group_id' => 'sometimes|integer|exists:groups,id',
            'department_id' => 'sometimes|integer|exists:departments,id',
            'type' => 'sometimes|string|max:255',
            'hours' => 'sometimes|integer|min:1',
            'semester' => 'sometimes|string|max:50',
            'year' => 'sometimes|integer',
        ]);
    
        $plannedLoad = PlannedLoad::findOrFail($id);
        $plannedLoad->update($data);
    
        $plannedLoad = PlannedLoad::with([
            'teacher.department',
            'discipline',
            'group',
            'department',
        ])->findOrFail($id);
    
        return response()->json($plannedLoad);
    }
    

    public function destroy(PlannedLoad $plannedLoad)
    {
        $plannedLoad->delete();

        return response()->noContent();
    }
}



