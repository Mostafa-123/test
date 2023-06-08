<?php


use App\Http\Controllers\AuthController;
use App\Http\Controllers\InteractionsController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\Api\User\UserController;
use App\Http\Controllers\hallsController;
use App\Http\Controllers\Api\Owner\OwnerController;
use App\Http\Controllers\Api\Admin\AdminController;
use App\Http\Controllers\Api\Planner\PlannerController;
use App\Http\Controllers\Api\Supplier\SupplierController;
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

        Route::get('anyProfile/{email}', [AuthController::class, 'anyProfile']);

            });





    Route::group([],function (){
        Route::get('getHallComment/{hall_id}', [ InteractionsController::class,'getComment']);



        Route::post('bookRoom', [ BookingController::class,'bookRoom']);

        Route::get('/getAllPlannerPlans/{owner_id}', [PlannerController::class, 'getAllPlannerPlans']);
        Route::get('/getAllOwnerHalls/{owner_id}', [OwnerController::class, 'getAllOwnerHalls']);
        Route::get('/getAllSupplierServices/{supplier_id}', [SupplierController::class, 'getAllSupplierServices']);
        Route::get('/getAllServices', [SupplierController::class, 'getAllServices']);
        Route::get('/getService/{service_id}', [SupplierController::class, 'getService']);
        Route::get('/getAllflowers', [SupplierController::class, 'getAllflowers']);//1
        Route::get('/getAllzaffatAndDj', [SupplierController::class, 'getAllzaffatAndDj']);//1
        Route::get('/getAllcake', [SupplierController::class, 'getAllcake']);//1    SubService
        Route::get('/getAlljallery', [SupplierController::class, 'getAlljallery']);//1
        Route::get('/getAllcatering', [SupplierController::class, 'getAllcatering']);//1
        Route::get('/getAllbodycare', [SupplierController::class, 'getAllbodycare']);//1
        Route::get('/getAllcar', [SupplierController::class, 'getAllcar']);//1
        Route::get('/getAllSupplierFlowers/{supplier_id}', [SupplierController::class, 'getAllSupplierFlowers']);//1
        Route::get('/getAllSupplierZaffatAndDj/{supplier_id}', [SupplierController::class, 'getAllSupplierZaffatAndDj']);//1
        Route::get('/getSupplierAllcake/{supplier_id}', [SupplierController::class, 'getSupplierAllcake']);//1
        Route::get('/getSupplierAlljallery/{supplier_id}', [SupplierController::class, 'getSupplierAlljallery']);//1
        Route::get('/getSupplierAllcatering/{supplier_id}', [SupplierController::class, 'getSupplierAllcatering']);//1
        Route::get('/getSupplierAllbodycare/{supplier_id}', [SupplierController::class, 'getSupplierAllbodycare']);//1
        Route::get('/getSupplierAllcar/{supplier_id}', [SupplierController::class, 'getSupplierAllcar']);//1





        Route::get('/getAllHalls', [UserController::class, 'getAllHalls']);
        Route::get('/getHall/{hall_id}', [UserController::class, 'gethall']);
        Route::post('/getAllHallsByPrice', [OwnerController::class, 'getAllHallsByPrice']);
        Route::post('getAllHallsByName', [OwnerController::class, 'getAllHallsByName']);
        Route::post('getAllHallsByCountry', [OwnerController::class, 'getAllHallsByCountry']);
        Route::post('getAllHallsByCity', [OwnerController::class, 'getAllHallsByCity']);
        Route::post('getAllHallsByStreet', [OwnerController::class, 'getAllHallsByStreet']);
        Route::post('getAllHallsByType', [OwnerController::class, 'getAllHallsByType']);


        Route::get('getAllUsers', [AdminController::class, 'getAllUsers']);//1
        Route::get('getAllPlanners', [AdminController::class, 'getAllPlanners']);//1
        Route::get('getAllSuppliers', [AdminController::class, 'getAllSuppliers']);//1
        Route::get('getAllOwners', [AdminController::class, 'getAllOwners']);//1
        Route::get('getAllAdmins', [AdminController::class, 'getAllAdmins']);//1
        Route::get('getUserCount', [AdminController::class, 'getUserCount']);//1
        Route::get('getOwnersCount', [AdminController::class, 'getOwnersCount']);//1
        Route::get('getPlannersCount', [AdminController::class, 'getPlannersCount']);//1
        Route::get('getSuppliersCount', [AdminController::class, 'getSupplierCount']);//1


        Route::post('addHall_WITHOUT_Token', [OwnerController::class, 'addHallRequestsToOWNER']);





        Route::get('/Offers', [OfferController::class, 'viewAll']);
            });
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




Route::any('{url}',function (){
    return response()->json('this url not found', 401);
})->where('url','.*')->middleware('api');







