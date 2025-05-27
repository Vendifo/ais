<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\MethodistController;
use App\Http\Controllers\TeacherController;
use App\Http\Middleware\AuthApiMiddleware;
use App\Http\Controllers\PlannedLoadController;
use App\Http\Controllers\ActualLoadController;
use App\Http\Controllers\LoadComparisonController;

// 🔐 Аутентификация
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

// 🧪 Тестовые маршруты
Route::middleware('auth.api')->get('/user', function (Request $request) {
    return response()->json($request->user());
});

Route::middleware('auth.api')->get('test', function () {
    return response()->json(['status' => 'OK']);
});

Route::get('/test', function () {
    return response()->json(['ok' => true]);
})->middleware(AuthApiMiddleware::class);

// 👑 Админка
Route::middleware(['auth.api', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index']);

    Route::apiResource('departments', \App\Http\Controllers\Api\DepartmentController::class);
    Route::apiResource('disciplines', \App\Http\Controllers\Api\DisciplineController::class);
    Route::apiResource('groups', \App\Http\Controllers\Api\GroupController::class);
    Route::apiResource('teachers', \App\Http\Controllers\Api\TeacherController::class);
});

// 🧾 Методист
Route::middleware(['auth.api', 'role:methodist'])->group(function () {
    Route::get('/methodist/reports', [MethodistController::class, 'reports']);

    // Плановая нагрузка
    Route::prefix('loads')->group(function () {
        Route::apiResource('planned', PlannedLoadController::class);
    });
});

// 👨‍🏫 Преподаватель
Route::middleware(['auth.api', 'role:teacher'])->group(function () {
    Route::get('/teacher/load', [TeacherController::class, 'load']);

    // Фактическая нагрузка
    Route::prefix('loads')->group(function () {
        Route::apiResource('actual', ActualLoadController::class);
    });
});

// 📊 Сравнение нагрузки доступно всем авторизованным
Route::middleware('auth.api')->get('/loads/compare', [LoadComparisonController::class, 'index']);