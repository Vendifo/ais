<?php

namespace App\Http\Controllers;

use App\Models\PlannedLoad;
use Illuminate\Http\Request;
use App\Rules\TeacherExists;


class PlannedLoadController extends Controller
{
    public function index() {
        return PlannedLoad::with(['teacher', 'discipline', 'group'])->get();
    }

    public function store(Request $request) {
        $data = $request->validate([
            'teacher_id' => ['required', 'integer', new TeacherExists],
            'discipline_id' => 'required|exists:disciplines,id',
            'group_id' => 'required|exists:groups,id',
            'type' => 'required|string',
            'hours' => 'required|integer|min:1',
            'semester' => 'required|string',
            'year' => 'required|integer',
        ]);
    
        $data['created_by'] = auth()->id();
    
        return PlannedLoad::create($data);
    }

    public function show(PlannedLoad $plannedLoad) {
        return $plannedLoad->load(['teacher', 'discipline', 'group']);
    }

    public function update(Request $request, PlannedLoad $plannedLoad) {
        $data = $request->validate([
            'type' => 'string',
            'hours' => 'integer|min:1',
            'semester' => 'string',
            'year' => 'integer',
        ]);

        $plannedLoad->update($data);
        return $plannedLoad;
    }

    public function destroy(PlannedLoad $plannedLoad) {
        $plannedLoad->delete();
        return response()->noContent();
    }
}