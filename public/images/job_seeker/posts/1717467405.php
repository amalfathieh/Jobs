<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

use function Clue\StreamFilter\fun;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('testNotification',[UserController::class,'noti']);

Route::get('createCV', 'SeekerController@createCV');

Route::middleware('web')->group(function () {
});
Route::get('login-google', [SocialAuthController::class, 'redirectToProvider']);
Route::get('auth/google/callback', [SocialAuthController::class, 'handleCallback']);
