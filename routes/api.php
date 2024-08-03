<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\ApplyController;
use App\Http\Controllers\SaveController;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OpportunityController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SeekerController;
use App\Http\Controllers\UserController;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use function Clue\StreamFilter\fun;

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

Route::middleware('web')->group(function () {
    Route::get('login-google', [SocialAuthController::class, 'redirectToProvider']);
    Route::get('auth/google/callback', [SocialAuthController::class, 'handleCallback']);
});

// Routes need auth //
Route::middleware(['auth:sanctum'])->group(function () {
    // Routes common with all //
    Route::controller(UserController::class)->group(function () {
        Route::get('getMyInfo', 'getMyInfo');

        Route::put('update', 'update')->middleware('can:edit user');

        Route::get('logout', 'logout');

        Route::post('checkPassword', 'checkPassword');

        Route::post('resetPassword', 'resetPassword');

        Route::delete('delete', 'delete');

//        Route::get('test/{token}', 'noti');

        Route::get('search/{search}', 'search');

        Route::post('device_token', 'storeToken');
        Route::get('user/{id}','getUserProfile');

        Route::get('firebase', 'FirebaseController@index');
    });

    Route::controller(ReportController::class)->prefix('report')->group(function () {
        Route::post('reportUser/{id}', 'reportUser')->middleware('can:user report create');
        Route::post('reportPost/{id}', 'reportPost')->middleware('can:user report create');
        Route::post('reportOpportunity/{id}', 'reportOpportunity')->middleware('can:user report create');

        Route::get('getReports/{type}', 'getReports')->middleware('can:user report view');
        Route::get('getInfo/{type}', 'getInfo')->middleware('can:user report view');
        Route::post('response/{id}', 'response')->middleware('can:user report view');
        Route::delete('delete/{id}', 'delete')->middleware('can:user report delete');
    });

    Route::controller(PostController::class)->group(function () {
        Route::get('allPosts', 'allPosts')->middleware('can:posts view');
    });

    Route::controller(OpportunityController::class)->group(function () {
        Route::get('allOpportunities', 'allOpportunities')->middleware('can:opportunities view');
        Route::delete('opportunity/delete/{id}', 'delete')->middleware('can:opportunity delete');
        Route::get('getOpp', 'getAllOpp')->middleware('can:opportunities view');
        Route::get('someInfo', 'someInfo')->middleware('can:opportunities view');
        Route::get('proposed_Jobs' , 'proposed_Jobs');
    });
        // Chat
        Route::controller(ChatController::class)->group(function () {
            Route::post('create', 'sendMessage');
            Route::post('displaysChats', 'allChats');
            Route::get('displayMessages/{chat_id}','shawAllMessages');
        });

        // Follow
        Route::controller(FollowController::class)->group(function () {
            Route::get('follow/{user_id}', 'follow');
            Route::get('followers/{user_id}','showFollowers');
            Route::get('followings/{user_id}','showFollowings');
        });

        // Notifications
        Route::controller(NotificationController::class)->prefix('notification')->group(function () {
            Route::get('display','displayNotification');
            Route::post('getContent','getNotificationContent');
            Route::delete('delete','delete');
            Route::get('makeRead','makeAsRead');
            Route::post('testStore', 'testStore');
        });

    // Routes common are over //

    // Company routes //
    //جلب الشركات المقترحة
    Route::get('proposed_Companies' , [CompanyController::class,'proposed_Companies']);

    Route::middleware('can:company create')->prefix('company')->group(function () {
        Route::controller(CompanyController::class)->group(function () {
            Route::post('create', 'createCompany');
            Route::post('update','update');
            Route::delete('delete', 'delete');
        });

        Route::controller(OpportunityController::class)->prefix('opportunity')->group(function () {
            Route::delete('delete/{id}', 'delete')->middleware('can:opportunity delete');

            Route::middleware('can:opportunity create')->group(function () {
                Route::post('addOpportunity', 'addOpportunity');
                Route::put('updateOpportunity/{id}', 'updateOpportunity');
            });

            Route::get('getMyOpportunities', 'getMyOpportunities')->middleware('can:opportunities view');
            Route::get('getCompanyOpportunities/{id}', 'getCompanyOpportunities')->middleware('can:opportunities view');
            Route::get('getOpportunityInfo/{id}', 'getOpportunityInfo')->middleware('can:opportunities view');
            Route::get('allOpportunities', 'allOpportunities')->middleware('can:opportunities view');
        });
    });
    // Company routes are over //

    // Seeker routes //
    Route::controller(SeekerController::class)->prefix('seeker')->group(function () {
        Route::middleware('can:seeker create')->group(function () {
            Route::post('create', 'create');
            Route::put('update', 'update');
        });
        Route::post('createCV', 'createCV');
    });

    // Apply,       To call this: api/apply/
    Route::controller(ApplyController::class)->prefix('apply')->group(function () {
        Route::post('{id}', 'apply')->middleware('can:request create');
        Route::post('update/{id}', 'update')->middleware('can:request edit');
        Route::get('getMyApplies', 'getMyApplies')->middleware('can:request view');

        Route::post('updateStatus/{id}', 'updateStatus')->middleware('can:status edit');
        Route::get('getApplies', 'getApplies')->middleware('can:request view');
        Route::delete('delete/{id}', 'delete')->middleware('can:request delete');
    });

    // Post        To call this: api/post/
    Route::controller(PostController::class)->prefix('post')->group(function () {
        Route::middleware('can:post create')->group(function () {
            Route::post('create', 'create');
            Route::put('edit/{post_id}' , 'edit');
        });
        Route::get('viewUserPosts/{id}','userPosts')->middleware('can:posts view');
        Route::get('getPostInfo/{id}','postInfo')->middleware('can:posts view');
        Route::delete('delete/{id}','delete')->middleware('can:post delete');
    });

    Route::controller(SaveController::class)->group(function () {
        Route::get('save/{opportunity_id}','saveOpportunity');
        Route::get('getSave','getSavedItems');
    });
    // Seeker routes are over //

    // Admin Routes // Don't forget: api/admin/{}
    Route::prefix('admin')->group(function ()  {
        Route::controller(AdminController::class)->group(function (){
            Route::delete('removeUser/{id}', 'removeUser')->middleware('can:user delete');

            Route::post('banUser/{id}', 'banUser')->middleware('can:block user');
            Route::post('unBanUser/{id}', 'unBanUser')->middleware('can:block user');
            Route::get('getBans/{type}', 'getBans')->middleware('can:block user');
            Route::get('deleteExpiredBanned', 'deleteExpiredBanned')->middleware('can:block user');

            Route::middleware('can:user view')->group(function () {

                Route::get('getUsers/{type}', 'getUsers');

                Route::get('search/{user}','search');
            });

            Route::middleware('can:logs view')->group(function () {
                Route::get('logs', 'logs');
                Route::get('getLogsEmployees', 'getLogsEmployees');

                Route::get('countUsers', 'countUsers');
                Route::get('countPOA', 'countPOA');

                Route::get('lineChartByDay', 'lineChartByDay');
                Route::get('lineChartByWeek', 'lineChartByWeek');
                Route::get('lineChartByMonth', 'lineChartByMonth');
            });

            // api/admin/news/
            Route::controller(NewsController::class)->prefix('news')->group(function () {

                Route::middleware('can:news create')->group(function () {
                    Route::post('create', 'create');
                    Route::put('update/{id}', 'update');
                });
                Route::delete('delete/{id}', 'delete')->middleware('can:news delete');

                Route::get('getNews', 'getNews')->middleware('can:news view');
            });
        });

        // api/admin/employee/{}
        Route::controller(EmployeeController::class)->prefix('employee')->group(function () {
            Route::middleware('can:employee control')->group(function () {
                Route::post('addEmployee','add');
            });
            Route::post('editInfo','edit');
            Route::get('employees','getEmployee')->middleware('can:employee view');
        });

        // Roles
        // api/admin/role/
        Route::middleware('can:role control')->controller(RoleController::class)->prefix('role')->group(function () {
            Route::get('allRoles', 'allRoles');
            Route::get('getRoles', 'getRoles');
            Route::get('getRolesForUserUpgrade', 'rolesForUserUpgrade');

            Route::post('addRole', 'addRole');
            Route::put('editRole/{id}', 'editRole');
            Route::delete('deleteRole/{id}', 'deleteRole');

            Route::put('editUserRoles/{id}', 'editUserRoles');
        });
    });

    // Admin routes are over //
});
// Routes need auth are over //

// Routes don't need auth //
Route::controller(UserController::class)->group(function () {
    Route::post('register', 'register');

    Route::get('ee/{date}', 'ee');

    // If code is expired, class this route
    Route::post('sendCode', 'sendCodeVerification');

    Route::post('login', 'login');

    // Verify
    Route::post('verifyAccount', 'verifyAccount');

    //       For Reset Password If user forgot his password
    Route::post('checkCode', 'checkCode');

    Route::post('forgotPassword', 'sendCode');

    Route::post('rePassword', 'rePassword');
});
// Routes don't need auth are over //
