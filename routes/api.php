<?php

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\SeekerController;
use App\Http\Controllers\UserController;
use App\Http\Requests\postRequest;
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

    // If code is expired, class this route
    Route::post('sendCode', 'sendCodeVerification');

    Route::post('login', 'login');

    Route::get('logout', 'logout')->middleware('auth:sanctum');

    Route::delete('delete', 'delete')->middleware('auth:sanctum');

    // Verify
    Route::post('verifyAccount', 'verifyAccount');

    //       For Reset Password
    Route::post('checkCode', 'checkCode');

    Route::post('forgotPassword', 'sendCode');

    Route::post('resetPassword', 'resetPassword');
});

Route::controller(CompanyController::class)->middleware(['auth:sanctum'])->prefix('company')->group(function () {
    Route::post('create', 'createCompany');

    Route::post('addOpportunity', 'addOpportunity');

});

Route::middleware(['auth:sanctum'])->controller(SeekerController::class)->prefix('seeker')->group(function () {
    Route::post('create', 'create');
    Route::post('update','update');
});
Route::middleware(['auth:sanctum'])->controller(postRequest::class)->group(function () {
    Route::post('create', 'create');

});
