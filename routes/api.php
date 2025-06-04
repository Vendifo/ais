<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Middleware\AuthApiMiddleware;
use App\Http\Controllers\PlannedLoadController;
use App\Http\Controllers\ActualLoadController;
use App\Http\Controllers\LoadComparisonController;
use App\Http\Controllers\ReportController;

// Аутентификация
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth.api')->get('/user', function (Request $request) {
    $user = $request->user()->load('roles');
    return response()->json([
        'user' => $user,
        'roles' => $user->roles->pluck('name'),
    ]);
});


// Админка
Route::middleware(['auth.api', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index']);

    Route::apiResource('departments', \App\Http\Controllers\Api\DepartmentController::class);
    Route::apiResource('disciplines', \App\Http\Controllers\Api\DisciplineController::class);
    Route::apiResource('groups', \App\Http\Controllers\Api\GroupController::class);

    Route::apiResource('users', \App\Http\Controllers\Api\UserController::class);
    Route::apiResource('roles', \App\Http\Controllers\Api\RoleController::class);
});

// Плановая нагрузка (методист + админ)
Route::middleware(['auth.api', 'role:methodist|admin'])->prefix('loads')->group(function () {
    Route::apiResource('planned', PlannedLoadController::class);
});

// Фактическая нагрузка (преподаватель + админ)
Route::middleware(['auth.api', 'role:teacher|admin'])->prefix('loads')->group(function () {
    Route::apiResource('actual', ActualLoadController::class);
});

// Общие отчёты (все авторизованные пользователи)
Route::middleware('auth.api')->group(function () {
    Route::get('/loads/compare', [LoadComparisonController::class, 'index']);

    Route::prefix('reports')->group(function () {
        Route::get('/workloads/teachers', [ReportController::class, 'workloadComparison']);
        Route::get('/workloads/disciplines', [ReportController::class, 'workloadByDiscipline']);
        Route::get('/workloads/departments', [ReportController::class, 'workloadByDepartment']);
        Route::get('/workloads/types', [ReportController::class, 'workloadByType']);
    });
});

Route::middleware(['auth.api', 'role:admin|methodist'])->get('/admin/teachers', [\App\Http\Controllers\Api\UserController::class, 'getTeachers']);





