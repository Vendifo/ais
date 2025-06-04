<?php

namespace App\Http\Controllers;

use App\Models\ActualLoad;
use Illuminate\Http\Request;
use App\Rules\TeacherExists;

class ActualLoadController extends Controller
{
    public function index()
    {
        return ActualLoad::with(['teacher', 'discipline', 'group', 'plannedLoad'])->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'planned_load_id' => 'nullable|exists:planned_loads,id',
            'teacher_id' => ['required', 'integer', new TeacherExists],
            'discipline_id' => 'required|exists:disciplines,id',
            'group_id' => 'required|exists:groups,id',
            'type' => 'required|string',
            'hours' => 'required|integer|min:1',
            'semester' => 'required|string',
            'year' => 'required|integer',
            'comment' => 'nullable|string',
        ]);

        $actualLoad = ActualLoad::create($data);

        return $actualLoad->load(['teacher', 'discipline', 'group', 'plannedLoad']);
    }

    public function show($id)
    {
        $actualLoad = ActualLoad::with(['teacher', 'discipline', 'group', 'plannedLoad'])->findOrFail($id);

        return response()->json($actualLoad);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'planned_load_id' => 'sometimes|exists:planned_loads,id',
            'teacher_id' => ['sometimes', 'integer', new TeacherExists],
            'discipline_id' => 'sometimes|exists:disciplines,id',
            'group_id' => 'sometimes|exists:groups,id',
            'type' => 'sometimes|string',
            'hours' => 'sometimes|integer|min:1',
            'semester' => 'sometimes|string',
            'year' => 'sometimes|integer',
            'comment' => 'nullable|string',
        ]);

        $actualLoad = ActualLoad::findOrFail($id);
        $actualLoad->update($data);

        $actualLoad = ActualLoad::with(['teacher', 'discipline', 'group', 'plannedLoad'])->findOrFail($id);

        return response()->json($actualLoad);
    }

    public function destroy($id)
    {
        $actualLoad = ActualLoad::findOrFail($id);
        $actualLoad->delete();

        return response()->noContent();
    }
}
