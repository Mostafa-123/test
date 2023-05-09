<?php

/* use App\Http\Controllers\AuthController; */

use App\Http\Controllers;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\Api\Admin\AdminController;
/* use App\Http\Controllers\Api\Admin\AuthController as AdminAuthController; */
use App\Http\Controllers\BookingController;
use App\Http\Controllers\AuthController;
/* use App\Http\Controllers\Api\User\AuthController as UserAuthController; */
use App\Http\Controllers\hallsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});
Route::group([
    'middleware' => ['api'],
    'prefix' => 'auth',
    'namespace' => 'Api',

], function ($router) {

    Route::group(['namespace' => 'Admin', 'middleware' => ['auth.guard:admin-api']], function () {
        /*   Route::post('adminLogin', [AuthController::class, 'adminLogin'])->name('login-admin');
        Route::post('adminLogout',[ AuthController::class,'adminLogout'])-> middleware(['auth.guard:admin-api']);
        Route::post('registerAdmin', [AuthController::class, 'registerAdmin']); */
        Route::get('adminProfile', [AuthController::class, 'adminProfile']);//1
        Route::post('updateAdmin/{admin_id}', [AuthController::class, 'updateAdmin']);//1
        // Route::get('adminphoto/{admin_id}',
        //     [AuthController::class, 'getAdminPhoto']
        // );


        //Offers
        Route::post('/createOffer',
            [OfferController::class, 'store']
        );
        Route::post('/updateOffer/{id}', [OfferController::class, 'update']);
        Route::delete('/destroyOffer/{id}', [OfferController::class, 'destroy']);
        Route::get('/Offers', [OfferController::class, 'viewAll']);
        Route::get('/Offers/{id}', [OfferController::class, 'viewOffer']);


        // Hall
        Route::apiResource('Hall', 'HallApiController');

        // Bookings
        Route::post('/bookings',
            [BookingController::class, 'bookRoom']
        );
        Route::post('/avl',
            [BookingController::class, 'getAvailableHalls']
        );
        Route::get('/viewBookings', [BookingController::class, 'viewBookings']);

        Route::post('/bookings/{bookingId}/confirm', [BookingController::class, 'confirmBooking']);
        Route::post('/bookings/{bookingId}/reject', [BookingController::class, 'rejectBooking'])->name('bookings.reject');
        Route::delete('/bookings/rejected', [BookingController::class, 'destroyRejectedBookings']);
    });
});





Route::group([
    'middleware' => ['api','auth.guard:admin-api'],
    'namespace'=>'Api',
    'prefix' => 'auth',

], function ($router) {

    Route::group(['namespace'=>'Admin'],function (){
        Route::post('addUser', [AdminController::class, 'addUser']);//1
        Route::post('addPlanner', [AdminController::class, 'addPlanner']);//1
        Route::post('addOwner', [AdminController::class, 'addOwner']);//1
        Route::post('addAdmin', [AdminController::class, 'addAdmin']);//1
        Route::get('getAllUsers', [AdminController::class, 'getAllUsers']);//1
        Route::get('getAllPlanners', [AdminController::class, 'getAllPlanners']);//1
        Route::get('getAllOwners', [AdminController::class, 'getAllOwners']);//1
        Route::get('getAllAdmins', [AdminController::class, 'getAllAdmins']);//1
        Route::get('getUserCount', [AdminController::class, 'getUserCount']);//1
        Route::get('getOwnersCount', [AdminController::class, 'getOwnersCount']);//1
        Route::get('getPlannersCount', [AdminController::class, 'getPlannersCount']);//1
        Route::get('getAdminsCount', [AdminController::class, 'getAdminsCount']);//1
        Route::get('getAllMembersCount', [AdminController::class, 'getAllMembersCount']);//1
        Route::post('deleteUser/{user_id}', [AdminController::class, 'deleteUser']);//1
        Route::post('deleteAdmin/{admin_id}', [AdminController::class, 'deleteAdmin']);//1
        Route::post('deletePlanner/{planner_id}', [AdminController::class, 'deletePlanner']);//1
        Route::post('deleteOwner/{owner_id}', [AdminController::class, 'deleteOwner']);//1
        Route::post('deleteHall/{hall_id}', [AdminController::class, 'destroyHall']);//1
        Route::post('deletePlan/{plan_id}', [AdminController::class, 'deletePlan']);//1
        Route::get('getplan/{plan_id}', [AdminController::class, 'getplan']);//1
        Route::get('gethall/{hall_id}', [AdminController::class, 'gethall']);//1
        Route::post('confirmHallRequest/{hall_id}', [AdminController::class, 'confirmHallRequest']);
        Route::post('rejectHallRequest/{hall_id}', [AdminController::class, 'rejectHallRequest']);
        Route::get('getConfirmedHalls', [AdminController::class, 'getConfirmedHalls']);//1
        Route::get('getUnConfirmedHalls', [AdminController::class, 'getUnConfirmedHalls']);//1
        Route::get('getCanceledHalls', [AdminController::class, 'getCanceledHalls']);//1
        Route::get('getAllHalls', [AdminController::class, 'getAllHalls']);//1




    });


        // Hall

});















Route::any('{url}',function (){
    return response()->json('this url not found', 401);
})->where('url','.*')->middleware('api');
Route::group([
    'middleware' => ['api'],
    'prefix' => 'auth',
    'namespace' => 'Api',

], function ($router) {
    Route::group(['namespace' => 'Admin'], function () {
        Route::get('adminphoto/{admin_id}', [AuthController::class, 'getAdminPhoto']);
    });
});








