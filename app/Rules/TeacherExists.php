<?php
// app/Rules/TeacherExists.php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\User;

class TeacherExists implements Rule
{
    public function passes($attribute, $value)
    {
        // Проверяем, что пользователь с таким ID существует и имеет роль teacher
        return User::where('id', $value)->whereHas('roles', function ($query) {
            $query->where('name', 'teacher');
        })->exists();
    }

    public function message()
    {
        return 'Выбранный :attribute должен быть действующим преподавателем.';
    }
}
