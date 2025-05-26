<?php

use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\MethodistController;
use App\Http\Controllers\TeacherController;
use App\Http\Middleware\AuthApiMiddleware;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth.api')->get('/user', function (Request $request) {
    return response()->json($request->user());
});

Route::middleware('auth.api')->get('test', function () {
    return response()->json(['status' => 'OK']);
});

Route::get('/test', function () {
    return response()->json(['ok' => true]);
})->middleware(AuthApiMiddleware::class);

Route::middleware(['auth.api', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'index']);
});

Route::middleware(['auth.api', 'role:methodist'])->group(function () {
    Route::get('/methodist/reports', [MethodistController::class, 'reports']);
});

Route::middleware(['auth.api', 'role:teacher'])->group(function () {
    Route::get('/teacher/load', [TeacherController::class, 'load']);
});