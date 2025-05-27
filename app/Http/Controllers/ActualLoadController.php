<?php
namespace App\Http\Controllers;

use App\Models\ActualLoad;
use Illuminate\Http\Request;
use App\Rules\TeacherExists;


class ActualLoadController extends Controller
{
    public function index() {
        return ActualLoad::with(['teacher', 'discipline', 'group', 'plannedLoad'])->get();
    }

    public function store(Request $request) {
        $data = $request->validate([
            'planned_load_id' => 'nullable|exists:planned_loads,id',
            'teacher_id' => ['required', 'integer', new TeacherExists],
            'discipline_id' => 'required|exists:disciplines,id',
            'group_id' => 'required|exists:groups,id',
            'type' => 'required|string',
            'hours' => 'required|integer|min:1',
            'semester' => 'required|string',
            'year' => 'required|integer',
            'comment' => 'nullable|string'
        ]);

        return ActualLoad::create($data);
    }

    public function show(ActualLoad $actualLoad) {
        return $actualLoad->load(['teacher', 'discipline', 'group', 'plannedLoad']);
    }

    public function update(Request $request, ActualLoad $actualLoad) {
        $data = $request->validate([
            'type' => 'string',
            'hours' => 'integer|min:1',
            'semester' => 'string',
            'year' => 'integer',
            'comment' => 'nullable|string',
        ]);

        $actualLoad->update($data);
        return $actualLoad;
    }

    public function destroy(ActualLoad $actualLoad) {
        $actualLoad->delete();
        return response()->noContent();
    }
}