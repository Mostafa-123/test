<?php


use App\Http\Controllers\Api\Owner\OwnerController;
use App\Http\Controllers\Api\Owner\OwnerAuthController;
use App\Http\Controllers\AuthController;
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
    'middleware' => ['api','auth.guard:owner-api','VerifyToken:owner-api'],
    'prefix' => 'auth',
    'namespace'=>'Api',

], function ($router) {

    Route::group(['namespace'=>'Owner'],function (){
        Route::post('updateOwner/{owner_id}', [AuthController::class, 'updateOwner']);//1
        Route::get('ownerProfile', [AuthController::class, 'ownerProfile']);//1
            });

    Route::group(['namespace'=>'Owner'],function (){
        // header('Access-Control-Allow-Origin: http://localhost:3000');
        // header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        // header('Access-Control-Allow-Headers: Content-Type, X-Auth-Token, Origin, Authorization');   // Hall   // Hall
        Route::post('/addHall', [OwnerController::class, 'addHallRequests']);//1
        Route::post('/deleteHall/{hall_id}', [OwnerController::class, 'destroyHall']);//1
        Route::get('/getHall/{hall_id}', [OwnerController::class, 'gethall']);//1
        Route::get('/getAllHalls', [OwnerController::class, 'getAllHalls']);//1
        Route::get('/getAllOwnerHalls/{owner_id}', [OwnerController::class, 'getAllOwnerHalls']);//1
        Route::get('/deleteAllOwnerHalls/{owner_id}', [OwnerController::class, 'deleteAllOwnerHalls']);
        Route::post('/updateHall/{hall_id}', [OwnerController::class, 'updateHall']);//1
        Route::post('addPhotoToMyhall/{hall_Id}', [OwnerController::class, 'addPhotoToMyhall']);//1
        Route::post('addVideoToMyhall/{hall_Id}', [OwnerController::class, 'addVideoToMyhall']);//1
        Route::post('getAllHallsByPrice', [OwnerController::class, 'getAllHallsByPrice']);//1
        Route::post('getAllHallsByName', [OwnerController::class, 'getAllHallsByName']);//1
        Route::post('getAllHallsByCity', [OwnerController::class, 'getAllHallsByCity']);//1
        Route::post('getAllHallsByCountry', [OwnerController::class, 'getAllHallsByCountry']);//1
        Route::post('getAllHallsByStreet', [OwnerController::class, 'getAllHallsByStreet']);//1
        Route::post('getAllHallsByType', [OwnerController::class, 'getAllHallsByType']);//1
        Route::get('DestroyAllHallRequest', [OwnerController::class, 'DestroyAllHallRequest']);
        Route::get('destroyHallRequest/{request_id}', [OwnerController::class, 'destroyHallRequest']);
        Route::post('updateAllInfoOfHallRequest/{user_id}', [OwnerController::class, 'updateAllInfoOfHallRequest']);


        // Bookings
/*         Route::apiResource('bookings', 'BookingsApiController');
 */        Route::post('/bookings', [BookingController::class, 'bookRoom']);
           Route::post('/avl', [BookingController::class, 'getAvailableHalls']);
           Route::get('/viewBookings', [BookingController::class, 'viewBookings']);


           Route::get('/HallTotalRevenue', [BookingController::class, 'HallTotalRevenue']);
           Route::get('/calculateTotalPrice', [BookingController::class, 'calculateTotalPrice']);
           Route::get('/calculateRevenue', [BookingController::class, 'calculateRevenue']);




           Route::get('/getOwnerAllBookings', [BookingController::class, 'getOwnerAllBookings']);//1
           Route::get('/getOwnerConfirmedBookings', [BookingController::class, 'getOwnerConfirmedBookings']);//1
           Route::get('/getOwnerUnConfirmedBookings', [BookingController::class, 'getOwnerUnConfirmedBookings']);//1
           Route::get('/getOwnerCancelledBookings', [BookingController::class, 'getOwnerCancelledBookings']);//1

           Route::post('/confirmBooking/{bookingId}', [BookingController::class, 'confirmBooking']);
           Route::post('/rejectBooking/{bookingId}', [BookingController::class, 'rejectBooking'])->name('bookings.reject');
           Route::delete('/bookings/rejected', [BookingController::class, 'destroyRejectedBookings']);
        });

});





















Route::group([
    'middleware' => ['api'],
    'namespace'=>'Api',
    'prefix' => 'auth',
], function ($router) {
    Route::group(['namespace'=>'Owner'],function (){
        Route::get('ownerphoto/{owner_id}', [AuthController::class, 'getOwnerPhoto']);  // Hall   // Hall
        });
});

Route::group([
    'middleware' => ['api'],
    'namespace'=>'Api',
    'prefix' => 'hall',

], function ($router) {
    Route::group([],function (){
        Route::get('hallphoto/{hall_id}/{photo_id}', [OwnerController::class, 'getHallPhoto']);
        Route::get('hallvideo/{hall_id}/{video_id}', [OwnerController::class, 'getHallVideo']);   // Hall   // Hall
        });
});
Route::any('{url}',function (){
    return response()->json('this url not found', 401);
})->where('url','.*')->middleware('api');
















