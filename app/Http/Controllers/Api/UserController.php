<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        // Список пользователей с ролями и кафедрой
        $users = User::with(['roles', 'department'])->paginate(20);
        return response()->json($users);
    }

    public function show($id)
    {
        $user = User::with(['roles', 'department'])->findOrFail($id);
        return response()->json($user);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'department_id' => 'nullable|exists:departments,id',
            'roles' => 'array',
            'roles.*' => 'exists:roles,id',
        ]);

        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);
        if (!empty($data['roles'])) {
            $user->roles()->sync($data['roles']);
        }

        return response()->json($user, 201);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => ['sometimes', 'required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'sometimes|nullable|string|min:6',
            'department_id' => 'nullable|exists:departments,id',
            'roles' => 'array',
            'roles.*' => 'exists:roles,id',
        ]);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        if (isset($data['roles'])) {
            $user->roles()->sync($data['roles']);
        }

        return response()->json($user);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->roles()->detach();
        $user->delete();

        return response()->json(null, 204);
    }
    public function getTeachers()
    {
        $teachers = User::whereHas('roles', function ($query) {
            $query->where('name', 'teacher');
        })->with('department:id,name')  // подгружаем кафедру, только id и name
            ->get(['id', 'name', 'department_id']); // выбираем department_id для связи

        return response()->json($teachers);
    }

}
