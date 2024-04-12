<?php

use App\Http\Controllers\Auth\AuthController as AuthAuthController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\system\MemberController;
use App\Http\Controllers\system\VikobaController;
use App\Http\Controllers\V2\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/bounce', function (Request $request) {
    return response()-> json($request->all());
})->withoutMiddleware(['auth:api', 'auth']);

Route::middleware(['AuthRequired', 'auth:api'])->group(function () {
    // Delivery routes 

    // Route::get('/auth', [AuthController::class, 'validToken']);
    Route::group(["prefix" => "member", "as" => "member."], function () {
        Route::get('/', [MemberController::class, 'index']);
    });
    Route::group(["prefix" => "auth", "as" => "auth."], function () {
        Route::post('/login', [LoginController::class, 'login']);
    });
});
Route::group(["prefix" => "vikoba", "as" => "vikoba."], function () {
    Route::get('/', [VikobaController::class, 'index']);
});

Route::post('/login', [LoginController::class, 'login']);
Route::post('/register', RegisterController::class);