<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\OpportunityController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\RoleController;
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

    Route::middleware('auth:sanctum')->group(function () {
        Route::put('update', 'update');

        Route::get('logout', 'logout');

        Route::post('checkPassword', 'checkPassword');

        Route::post('resetPassword', 'resetPassword');

        Route::delete('delete', 'delete');

        Route::post('fcm-token', 'updateToken');

        Route::get('test/{token}', 'noti');

    });


    // Verify
    Route::post('verifyAccount', 'verifyAccount');

    //       For Reset Password If user forgot his password
    Route::post('checkCode', 'checkCode');

    Route::post('forgotPassword', 'sendCode');

    Route::post('rePassword', 'rePassword');
});

Route::controller(CompanyController::class)->middleware(['auth:sanctum'])->prefix('company')->group(function () {
    Route::post('create', 'createCompany');
    Route::post('update','update');
    Route::post('addOpportunity', 'addOpportunity');

});

Route::middleware(['auth:sanctum'])->controller(SeekerController::class)->prefix('seeker')->group(function () {
    Route::post('create', 'create');
    Route::post('update','update');

    Route::get('createCV', 'createCV');
});
Route::middleware(['auth:sanctum'])->controller(PostController::class)->group(function () {
    Route::post('create', 'create');

});
Route::middleware(['auth:sanctum'])->controller(ChatController::class)->group(function () {
    Route::post('create', 'sendMessage');
    Route::post('displaysChats', 'allChats');
    Route::get('displayMessages/{chat_id}','shawAllMessages');
});

// Admin Routes

Route::middleware(['auth:sanctum'])->prefix('admin')->group(function ()  {
    Route::controller(AdminController::class)->group(function (){
        Route::delete('removeUser', 'removeUser')->middleware('can:delete user');
        Route::delete('removePost', 'removePost')->middleware('can:delete post');

        Route::post('blockUser', 'blockUser')->middleware('can:block user');

        Route::middleware('can:view users')->group(function () {

            Route::get('getUsers/{type}', 'getUsers')->middleware('can:view users');

            Route::post('addEmployee','addEmployee')->middleware('can:add employee');

            Route::get('search/{user}','searchByUsernameOrEmail')->middleware('can:view users');
        });
    });


    Route::controller(PostController::class)->group(function () {
        Route::get('allPosts', 'allPosts')->middleware('can:view posts');
    });

    Route::controller(OpportunityController::class)->group(function () {
        Route::get('allOpportunities', 'allOpportunities')->middleware('can:view opportunities');
    });

});

Route::middleware(['auth:sanctum'])->controller(FollowController::class)->group(function () {
    Route::get('follow/{user_id}', 'followUser');
    Route::get('unFollow/{user_id}', 'unFollowUser');
    Route::get('followers/{user_id}','showFollowers');
    Route::get('followings/{user_id}','showFollowings');

});


// Roles

Route::middleware(['auth:sanctum'])->controller(RoleController::class)->prefix('role')->group(function () {
    Route::get('allRoles', 'allRoles')->middleware('can:view roles');
    Route::post('addRole', 'addRole')->middleware('can:add role');
    Route::put('editRole', 'editRole')->middleware('can:edit role');
    Route::post('deleteRole', 'deleteRole')->middleware('can:delete role');

    Route::post('editUserRoles', 'editUserRoles')->middleware('can:add role');
});
