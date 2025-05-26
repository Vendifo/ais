<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Discipline;
use Illuminate\Http\Request;

class DisciplineController extends Controller
{
    public function index()
    {
        return response()->json(Discipline::with('department')->get());
    }

    public function show($id)
    {
        $discipline = Discipline::with('department')->findOrFail($id);
        return response()->json($discipline);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:disciplines|max:255',
            'description' => 'nullable|string',
            'department_id' => 'required|exists:departments,id',
        ]);

        $discipline = Discipline::create($validated);
        return response()->json($discipline, 201);
    }

    public function update(Request $request, $id)
    {
        $discipline = Discipline::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|unique:disciplines,name,' . $discipline->id . '|max:255',
            'description' => 'nullable|string',
            'department_id' => 'required|exists:departments,id',
        ]);

        $discipline->update($validated);
        return response()->json($discipline);
    }

    public function destroy($id)
    {
        $discipline = Discipline::findOrFail($id);
        $discipline->delete();
        return response()->json(null, 204);
    }
}
