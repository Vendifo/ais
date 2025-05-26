<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MethodistController extends Controller
{
    public function reports()
    {
        return response()->json(['message' => 'Отчёты методиста доступны!']);
    }
}
