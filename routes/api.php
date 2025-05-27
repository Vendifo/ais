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
use App\Http\Controllers\ReportController;

// 🔐 Аутентификация
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth.api')->get('/user', function (Request $request) {
    $user = $request->user()->load('roles'); // Загружаем роли

    return response()->json([
        'user' => $user,
        'roles' => $user->roles->pluck('name'), // список ролей в виде массива строк
    ]);
});


// 👑 Админка
Route::middleware(['auth.api', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index']);

    Route::apiResource('departments', \App\Http\Controllers\Api\DepartmentController::class);
    Route::apiResource('disciplines', \App\Http\Controllers\Api\DisciplineController::class);
    Route::apiResource('groups', \App\Http\Controllers\Api\GroupController::class);
    Route::apiResource('teachers', \App\Http\Controllers\Api\TeacherController::class);

    // Добавляем пользователей и роли
    Route::apiResource('users', \App\Http\Controllers\Api\UserController::class);
    Route::apiResource('roles', \App\Http\Controllers\Api\RoleController::class);
});


// 🧾 Методист
Route::middleware(['auth.api', 'role:methodist'])->group(function () {
    Route::get('/methodist/reports', [MethodistController::class, 'reports']);

    Route::prefix('loads')->group(function () {
        Route::apiResource('planned', PlannedLoadController::class);
    });
});

// 👨‍🏫 Преподаватель
Route::middleware(['auth.api', 'role:teacher'])->group(function () {
    Route::get('/teacher/load', [TeacherController::class, 'load']);

    Route::prefix('loads')->group(function () {
        Route::apiResource('actual', ActualLoadController::class);
    });
});

// 📊 Сравнение и отчёты — доступны всем авторизованным пользователям
Route::middleware('auth.api')->group(function () {
    Route::get('/loads/compare', [LoadComparisonController::class, 'index']);

    Route::prefix('reports')->group(function () {
        Route::get('/workloads/teachers', [ReportController::class, 'workloadComparison']);
        Route::get('/workloads/disciplines', [ReportController::class, 'workloadByDiscipline']);
        Route::get('/workloads/departments', [ReportController::class, 'workloadByDepartment']);
        Route::get('/workloads/types', [ReportController::class, 'workloadByType']);

        // Заготовки на будущее
        // Route::get('/export/pdf', [ReportController::class, 'exportPdf']);
        // Route::get('/export/excel', [ReportController::class, 'exportExcel']);
    });
});
