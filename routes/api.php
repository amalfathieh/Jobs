<?php

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\SeekerController;
use App\Http\Controllers\UserController;
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


Route::controller(UserController::class)->group(function () {
    Route::post('register', 'register');

    Route::post('sendCode', 'sendCodeVerification');

    Route::post('login', 'login');

    Route::get('logout', 'logout')->middleware('auth:sanctum');

    Route::post('checkCode', 'checkCode');

    Route::post('forgotPassword', 'sendCode');

    Route::post('resetPassword', 'resetPassword');
});

Route::controller(CompanyController::class)->middleware(['auth:sanctum'])->prefix('company')->group(function () {
    Route::post('create', 'createCompany');
});

Route::middleware(['auth:sanctum'])->controller(SeekerController::class)->prefix('seeker')->group(function () {
    Route::post('creat', 'creat');
    Route::post('update','update');
});
