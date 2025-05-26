<?php

use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
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

