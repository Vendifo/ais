<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Group;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function index()
    {
        return response()->json(Group::with('department')->get());
    }

    public function show($id)
    {
        $group = Group::with('department')->findOrFail($id);
        return response()->json($group);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:groups|max:255',
            'year' => 'nullable|integer',
            'department_id' => 'required|exists:departments,id',
        ]);

        $group = Group::create($validated);
        return response()->json($group, 201);
    }

    public function update(Request $request, $id)
    {
        $group = Group::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|unique:groups,name,' . $group->id . '|max:255',
            'year' => 'nullable|integer',
            'department_id' => 'required|exists:departments,id',
        ]);

        $group->update($validated);
        return response()->json($group);
    }

    public function destroy($id)
    {
        $group = Group::findOrFail($id);
        $group->delete();
        return response()->json(null, 204);
    }
}
