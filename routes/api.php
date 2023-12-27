<?php

use App\Http\Controllers\system\MemberController;
use App\Http\Controllers\system\VikobaController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

    // Delivery routes 
    Route::group(["prefix" => "vikoba", "as" => "vikoba."], function () {
        Route::get('/', [VikobaController::class, 'index']);
    });
    Route::group(["prefix" => "member", "as" => "member."], function () {
        Route::get('/', [MemberController::class, 'index']);
    });