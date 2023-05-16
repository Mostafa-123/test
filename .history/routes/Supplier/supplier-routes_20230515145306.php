<?php
use App\Http\Controllers\Api\Supplier\SupplierController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use Illuminate\Support\Facades\Route;
Route::group([
    'middleware' => ['api','auth.guard:supplier-api'],
    'prefix' => 'auth',
    'namespace'=>'Api',

], function ($router) {
    Route::group(['namespace'=>'Supplier',],function (){
            //Route::post('loginPlanner', [PlannerAuthController::class, 'login'])->name('login-Planner');
           // Route::post('registerPlanner', [PlannerAuthController::class, 'registerPlanner']);
            Route::get('profileSupplier', [AuthController::class, 'supplierProfile']);
            Route::post('updateSupplier/{supplier_id}', [AuthController::class, 'updateSupplier']);
            //5555555555
           // Route::post('logoutPlanner',[ PlannerAuthController::class,'logout'])-> middleware(['auth.guard:planner-api']);
                });
        });
Route::group([
    'middleware' => ['api','auth.guard:supplier-api'],
    'prefix' => 'auth',
    'namespace' => 'Api',

], function ($router) {
    Route::group(['namespace' => 'Supplier',], function () {
        Route::post('addService', [SupplierController::class, 'addService']);//1
        Route::post('/deleteService/{id}', [SupplierController::class, 'deleteService']);//1
        Route::post('/updateService/{id}', [SupplierController::class, 'updateService']);//1
        Route::get('/getService/{service_id}', [SupplierController::class, 'getService']);//1
        Route::get('/getAllServices', [SupplierController::class, 'getAllServices']);//1
        Route::get('/getAllflowers', [SupplierController::class, 'getAllflowers']);//1
        Route::get('/getAllzaffatAndDj', [SupplierController::class, 'getAllzaffatAndDj']);//1
        Route::get('/getAllcake', [SupplierController::class, 'getAllcake']);//1    SubService
        Route::get('/getAlljallery', [SupplierController::class, 'getAlljallery']);//1
        Route::get('/getAllcatering', [SupplierController::class, 'getAllcatering']);//1
        Route::get('/getAllbodycare', [SupplierController::class, 'getAllbodycare']);//1
        Route::get('/getAllcar', [SupplierController::class, 'getAllcar']);//1
        Route::get('/getAllSupplierServices/{supplier_id}', [SupplierController::class, 'getAllSupplierServices']);//1
        Route::post('addPhotoToMyService/{service_Id}', [SupplierController::class, 'addPhotoToMyService']);//1
        Route::get('viewConfirmedSubRequests', [SupplierController::class, 'viewConfirmedSubRequests']);
        Route::get('viewCancelledviewSubRequests', [SupplierController::class, 'viewCancelledSubRequests']);
        Route::get('viewSubRequests', [SupplierController::class, 'viewSubRequests']);
        Route::post('confirmSubRequest/{bookingplanId}', [SupplierController::class, 'confirmSubRequest']);
        Route::post('rejectSubRequest/{bookingplanId}', [SupplierController::class, 'rejectSubRequest']);



        Route::get('/getSupplierAllSubReqests', [BookingController::class, 'getSupplierAllSubReqests']);//1
        Route::get('/getOwnerConfirmedBookings', [BookingController::class, 'getOwnerConfirmedBookings']);//1
        Route::get('/getOwnerUnConfirmedBookings', [BookingController::class, 'getOwnerUnConfirmedBookings']);//1
        Route::get('/getOwnerCancelledBookings', [BookingController::class, 'getOwnerCancelledBookings']);//1




    });
});















Route::group([
    'middleware' => ['api'],
    'namespace' => 'Api',
    'prefix' => 'auth',

], function ($router) {
    Route::group(['namespace' => 'Supplier',], function () {
        Route::get('supplierphoto/{supplier_id}', [AuthController::class, 'getSupplierPhoto']);
    });
});
Route::group([
    'middleware' => ['api'],
    'namespace' => 'Api',
    'prefix' => 'service',
], function ($router) {
    Route::group([], function () {
        Route::get('servicephoto/{service_id}/{photo_id}', [SupplierController::class, 'getServicePhoto']);
    });
});
Route::any('{url}',function (){
    return response()->json('this url not found', 401);
})->where('url','.*')->middleware('api');
