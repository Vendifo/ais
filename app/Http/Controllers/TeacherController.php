<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TeacherController extends Controller
{
    public function load()
    {
        return response()->json(['message' => 'Просмотр учебной нагрузки преподавателя']);
    }
}
