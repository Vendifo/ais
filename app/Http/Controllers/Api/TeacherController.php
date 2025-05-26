<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\User;



class TeacherController extends Controller
{
    public function index()
    {
        return response()->json(Teacher::with(['user', 'department'])->get());
    }

    public function show($id)
    {
        $teacher = Teacher::with(['user', 'department'])->findOrFail($id);
        return response()->json($teacher);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id|unique:teachers,user_id',
            'position' => 'nullable|string|max:255',
            'department_id' => 'required|exists:departments,id',
        ]);

        // Проверяем роль пользователя
        $user = User::find($validated['user_id']);
        if (!$user->hasRole('teacher')) {
            throw ValidationException::withMessages([
                'user_id' => ['Этот пользователь не имеет роль преподавателя.'],
            ]);
        }

        $teacher = Teacher::create($validated);
        return response()->json($teacher, 201);
    }

    public function update(Request $request, $id)
    {
        $teacher = Teacher::findOrFail($id);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id|unique:teachers,user_id,' . $teacher->id,
            'position' => 'nullable|string|max:255',
            'department_id' => 'required|exists:departments,id',
        ]);

        // Проверяем роль пользователя
        $user = User::find($validated['user_id']);
        if (!$user->hasRole('teacher')) {
            throw ValidationException::withMessages([
                'user_id' => ['Этот пользователь не имеет роль преподавателя.'],
            ]);
        }

        $teacher->update($validated);
        return response()->json($teacher);
    }

    public function destroy($id)
    {
        $teacher = Teacher::findOrFail($id);
        $teacher->delete();
        return response()->json(null, 204);
    }
}
