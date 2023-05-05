<?php

/* use App\Http\Controllers\AuthController; */
/* use App\Http\Controllers\Api\Admin\AuthController;
 */use App\http\Controllers\AuthController;
use App\Http\Controllers\Api\User\UserController;
use App\Http\Controllers\hallsController;
use App\Http\Controllers\Api\Owner\OwnerController;
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

    Route::group([],function (){
       /*  Route::post('login', [AuthController::class, 'login'])->name('login-admin'); */
        Route::post('logout',[ AuthController::class,'logout']);
        Route::post('switchLogin', [AuthController::class, 'switchLogin'])->name('login-admin');
        Route::post('switchRegister', [AuthController::class, 'switchRegister']);


            });





    Route::group([],function (){
        Route::get('halls/{hall_id}/getComment', [ InteractionsController::class,'getComment']);

        Route::get('/getAllHalls', [UserController::class, 'getAllHalls']);
        Route::get('/getHall/{hall_id}', [UserController::class, 'gethall']);
        Route::get('/getAllHallsByPrice/{max}/{min}', [OwnerController::class, 'getAllHallsByPrice']);
        Route::get('getAllHallsByName', [OwnerController::class, 'getAllHallsByName']);
        Route::get('getAllHallsByCountry/{country}', [OwnerController::class, 'getAllHallsByCountry']);
        Route::get('getAllHallsByCity/{city}', [OwnerController::class, 'getAllHallsByCity']);
        Route::get('getAllHallsByStreet/{street}', [OwnerController::class, 'getAllHallsByStreet']);
        Route::get('getAllHallsByType/{type}', [OwnerController::class, 'getAllHallsByType']);



        Route::get('/Offers', [OfferController::class, 'viewAll']);





            });


    Route::post('/refresh', [AuthController::class, 'refresh']);

});






/* Route::group([
    'middleware' => 'api',
], function ($router) {
    Route::get('/getHall/{id}', [hallsController::class, 'getHallController']);
    Route::get('/getHalls', [hallsController::class, 'getHallsController']);
    Route::post('/addHall', [hallsController::class, 'addHallController']);
    Route::post('/updateAllInfoOfHall/{id}', [hallsController::class, 'updateAllInfoOfHallController']);
    Route::post('/deleteHall/{id}', [hallsController::class, 'destroyHallController']);
    Route::post('/updateAnyInfoInHall/{id}', [hallsController::class, 'updateAnyInfoInHallController']);



}); */











