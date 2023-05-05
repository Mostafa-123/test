<?php

use App\Http\Controllers\Api\Admin\AuthController;
use App\Http\Controllers\Api\Owner\OwnerController;
use App\Http\Controllers\Api\Owner\OwnerAuthController;
use App\Http\Controllers\BookingController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\hallResource;


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

    Route::group(['namespace'=>'Owner'],function (){
        Route::post('loginOwner', [OwnerAuthController::class, 'login'])->name('login-Owner');
        Route::post('logoutOwner',[ OwnerAuthController::class,'logout'])-> middleware(['auth.guard:owner-api']);
        Route::post('registerOwner', [OwnerAuthController::class, 'registerOwner']);
        Route::post('updateOwner/{owner_id}', [OwnerAuthController::class, 'updateOwner']);
        Route::get('ownerProfile', [OwnerAuthController::class, 'ownerProfile']);
            });
});
Route::group([
    'middleware' => ['api'],
    'namespace'=>'Api',

], function ($router) {
    Route::group(['namespace'=>'Owner'],function (){
        Route::get('ownerphoto/{owner_id}', [OwnerAuthController::class, 'getOwnerPhoto']);
        Route::get('hallphoto/{hall_id}/{photo_id}', [OwnerController::class, 'getHallPhoto']);
        Route::get('hallvideo/{hall_id}/{video_id}', [OwnerController::class, 'getHallVideo']);   // Hall   // Hall
        Route::post('/addHall', [OwnerController::class, 'addHallRequests']);
        Route::get('/deleteHall/{hall_id}', [OwnerController::class, 'destroyHall']);
        Route::get('/deleteAllOwnerHalls/{owner_id_id}', [OwnerController::class, 'deleteAllOwnerHalls']);
        Route::get('/getHall/{hall_id}', [OwnerController::class, 'gethall']);
        Route::get('/getAllHalls', [OwnerController::class, 'getAllHalls']);
        Route::get('/getAllOwnerHalls/{owner_id}', [OwnerController::class, 'getAllOwnerHalls']);
        Route::get('/deleteAllOwnerHalls/{owner_id}', [OwnerController::class, 'deleteAllOwnerHalls']);
        Route::post('/updateHall/{hall_id}', [OwnerController::class, 'updateHall']);
        Route::post('addPhotoToMyhall/{hall_Id}', [OwnerController::class, 'addPhotoToMyhall']);
        Route::post('addVideoToMyhall/{hall_Id}', [OwnerController::class, 'addVideoToMyhall']);
        Route::get('getAllHallsByPrice/{min_price}/{max_price}', [OwnerController::class, 'getAllHallsByPrice']);
        Route::get('getAllHallsByName/{name}', [OwnerController::class, 'getAllHallsByName']);
        Route::get('getAllHallsByCity/{city}', [OwnerController::class, 'getAllHallsByCity']);
        Route::get('getAllHallsByType/{type}', [OwnerController::class, 'getAllHallsByType']);
        Route::get('/DestroyAllHallRequest', [OwnerController::class, 'DestroyAllHallRequest']);
        Route::get('destroyHallRequest/{request_id}', [OwnerController::class, 'destroyHallRequest']);
        Route::post('updateAllInfoOfHallRequest/{user_id}', [OwnerController::class, 'updateAllInfoOfHallRequest']);


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




