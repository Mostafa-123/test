<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\InteractionsController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\Api\User\UserAuthController;
use App\Http\Controllers\Api\User\UserController;
use App\Http\Controllers\AuthwController;
use App\Http\Controllers\hallsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::group([
    'middleware' => ['api', 'auth.guard:user-api'],
    'prefix' => 'auth',
    'namespace' => 'Api',

], function ($router) {
    Route::group(['namespace' => 'User',], function () {
        // Route::post('loginUser', [UserAuthController::class, 'login'])->name('login-user');
        // Route::post('registerUser', [UserAuthController::class, 'userRegister']);
        Route::get('userProfile', [AuthController::class, 'userProfile']);//1
        Route::post('updateUser/{user_id}', [AuthController::class, 'updateUser']);//1
       // Route::post('logoutUser', [UserAuthController::class, 'logout'])->middleware(['auth.guard:user-api']);
        // Route::get('/getHall/{hall_id}', [UserController::class, 'gethall']); //
        // Route::get('/getAllHalls', [UserController::class, 'getAllHalls']);


        Route::post('bookRoom', [ BookingController::class,'bookRoom']);
        Route::post('bookPlan', [ BookingController::class,'bookPlan']);
        Route::post('bookSubService', [ BookingController::class,'bookSubService']);



        Route::get('/getUserAllBookings', [BookingController::class, 'getUserAllBookings']);//1
        Route::get('/getUserConfirmedBookings', [BookingController::class, 'getUserConfirmedBookings']);//1
        Route::get('/getUserUnConfirmedBookings', [BookingController::class, 'getUserUnConfirmedBookings']);//1
        Route::get('/getUserCancelledBookings', [BookingController::class, 'getPlannerCancelledBookings']);//1

        Route::get('/getUserAllPlanRequests', [BookingController::class, 'getUserAllPlanRequests']);//1
        Route::get('/getUserConfirmedPlanRequests', [BookingController::class, 'getUserConfirmedPlanRequests']);//1
        Route::get('/getUserUnConfirmedPlanRequests', [BookingController::class, 'getUserUnConfirmedPlanRequests']);//1
        Route::get('/getUserCancelledPlanRequests', [BookingController::class, 'getPlannerCancelledPlanRequests']);//1

        Route::get('/getUserAllSubRequests', [BookingController::class, 'getUserAllSubRequests']);//1
        Route::get('/getUserConfirmedSubRequests', [BookingController::class, 'getUserConfirmedSubRequests']);//1
        Route::get('/getUserUnConfirmedSubRequests', [BookingController::class, 'getUserUnConfirmedSubRequests']);//1
        Route::get('/getUserCancelledSubRequests', [BookingController::class, 'getPlannerCancelledSubRequests']);//1



        Route::post('halls/{hall_id}/addLike', [InteractionsController::class, 'addLike']);
        Route::post('halls/{hall_id}/addComment', [InteractionsController::class, 'addComment']);
        Route::get('halls/{hall_id}/getComment', [InteractionsController::class, 'getComment']);
        Route::post('halls/{comment_id}/updateComment', [InteractionsController::class, 'updateComment']);
        Route::delete('halls/{comment_id}/deleteComment', [InteractionsController::class, 'deleteComment']);
    });
});




















Route::group([
    'middleware' => ['api'],
    'prefix' => 'auth',
    'namespace' => 'Api',

], function ($router) {
    Route::group(['namespace' => 'User',], function () {
        Route::get('userphoto/{user_id}', [AuthController::class, 'getUserPhoto']);
    });
});

Route::any('{url}',function (){
    return response()->json('this url not found', 401);
})->where('url','.*')->middleware('api');
