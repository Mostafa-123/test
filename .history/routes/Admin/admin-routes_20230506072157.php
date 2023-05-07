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
    'namespace'=>'Api',

], function ($router) {

    Route::group(['namespace'=>'Admin','middleware'=>['auth.guard:admin-api']],function (){
      /*   Route::post('adminLogin', [AuthController::class, 'adminLogin'])->name('login-admin');
        Route::post('adminLogout',[ AuthController::class,'adminLogout'])-> middleware(['auth.guard:admin-api']);
        Route::post('registerAdmin', [AuthController::class, 'registerAdmin']); */
        Route::get('adminProfile', [AuthController::class, 'adminProfile']);
        Route::post('updateAdmin/{admin_id}', [AuthController::class, 'updateAdmin']);
        Route::get('adminphoto/{admin_id}', [AuthController::class, 'getAdminPhoto']);


        //Offers
        Route::post('/createOffer', [OfferController::class, 'store']);
        Route::post('/updateOffer/{id}', [OfferController::class, 'update']);
        Route::delete('/destroyOffer/{id}', [OfferController::class, 'destroy']);
        Route::get('/Offers', [OfferController::class, 'viewAll']);
        Route::get('/Offers/{id}', [OfferController::class, 'viewOffer']);


        // Hall
        Route::apiResource('Hall', 'HallApiController');

        // Bookings
/*         Route::apiResource('bookings', 'BookingsApiController');
 */        Route::post('/bookings', [BookingController::class, 'bookRoom']);
           Route::post('/avl', [BookingController::class, 'getAvailableHalls']);
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
        Route::post('addUser', [AdminController::class, 'addUser']);
        Route::post('addPlanner', [AdminController::class, 'addPlanner']);
        Route::post('addOwner', [AdminController::class, 'addOwner']);
        Route::post('addAdmin', [AdminController::class, 'addAdmin']);
        Route::get('getAllUsers', [AdminController::class, 'getAllUsers']);
        Route::get('getAllPlanners', [AdminController::class, 'getAllPlanners']);
        Route::get('getAllOwners', [AdminController::class, 'getAllOwners']);
        Route::get('getAllAdmins', [AdminController::class, 'getAllAdmins']);
        Route::get('getUserCount', [AdminController::class, 'getUserCount']);
        Route::get('getOwnersCount', [AdminController::class, 'getOwnersCount']);
        Route::get('getPlannersCount', [AdminController::class, 'getPlannersCount']);
        Route::get('getAdminsCount', [AdminController::class, 'getAdminsCount']);
        Route::get('getAllMembersCount', [AdminController::class, 'getAllMembersCount']);
        Route::delete('deleteUser/{user_id}', [AdminController::class, 'deleteUser']);
        Route::delete('deleteAdmin/{admin_id}', [AdminController::class, 'deleteAdmin']);
        Route::delete('deletePlanner/{planner_id}', [AdminController::class, 'deletePlanner']);
        Route::delete('deleteOwner/{owner_id}', [AdminController::class, 'deleteOwner']);
        Route::delete('deleteHall/{hall_id}', [AdminController::class, 'destroyHall']);
        Route::delete('deletePlan/{plan_id}', [AdminController::class, 'deletePlan']);
        Route::get('getplan/{plan_id}', [AdminController::class, 'getplan']);
        Route::get('gethall/{hall_id}', [AdminController::class, 'gethall']);
        Route::get('confirmHallRequest/{hall_id}', [AdminController::class, 'confirmHallRequest']);
        Route::get('rejectHallRequest/{hall_id}', [AdminController::class, 'rejectHallRequest']);
        Route::get('getConfirmedHalls', [AdminController::class, 'getConfirmedHalls']);
        Route::get('getUnConfirmedHalls', [AdminController::class, 'getUnConfirmedHalls']);
        Route::get('getCanceledHalls', [AdminController::class, 'getCanceledHalls']);
        Route::get('getAllHalls', [AdminController::class, 'getAllHalls']);




    });


        // Hall

});








