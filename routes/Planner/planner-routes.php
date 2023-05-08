<?php
use App\Http\Controllers;
use App\Http\Controllers\Api\Planner\PlannerAuthController ;
use App\Http\Controllers\Api\Planner\PlannerController ;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
Route::group([
    'middleware' => ['api','auth.guard:planner-api'],
    'prefix' => 'auth',
    'namespace'=>'Api',

], function ($router) {
    Route::group(['namespace'=>'Planner',],function (){
            //Route::post('loginPlanner', [PlannerAuthController::class, 'login'])->name('login-Planner');
           // Route::post('registerPlanner', [PlannerAuthController::class, 'registerPlanner']);
            Route::get('profilePlanner', [AuthController::class, 'plannerProfile']);
            Route::post('updatePlanner/{planner_id}', [AuthController::class, 'updatePlanner']);
            //5555555555
           // Route::post('logoutPlanner',[ PlannerAuthController::class,'logout'])-> middleware(['auth.guard:planner-api']);
                });
        });
Route::group([
    'middleware' => ['api','auth.guard:planner-api'],
    'namespace' => 'Api',

], function ($router) {
    Route::group(['namespace' => 'Planner',], function () {
        Route::post('addPlan', [PlannerController::class, 'addPlan']);
        Route::post('/deletePlan/{id}', [PlannerController::class, 'deletePlan']);
        Route::post('/updatePlan/{id}', [PlannerController::class, 'updatePlan']);
        Route::get('/getPlan/{plan_id}', [PlannerController::class, 'getPlan']);
        Route::get('/getAllPlans', [PlannerController::class, 'getAllPlans']);
        Route::get('/getAllPlannerPlans/{owner_id}', [PlannerController::class, 'getAllPlannerPlans']);
        Route::post('addPhotoToMyplan/{plan_Id}', [PlannerController::class, 'addPhotoToMyplan']);
        Route::get('viewConfirmedBookingsPlans', [PlannerController::class, 'viewConfirmedBookingsPlans']);
        Route::get('viewCancelledBookingsPlans', [PlannerController::class, 'viewCancelledBookingsPlans']);
        Route::get('viewBookingsplans', [PlannerController::class, 'viewBookingsplans']);
        Route::post('confirmBookingPlan/{bookingplanId}', [PlannerController::class, 'confirmBookingPlan']);
        Route::post('rejectBookingPlan/{bookingplanId}', [PlannerController::class, 'rejectBookingPlan']);

    });
});















Route::group([
    'middleware' => ['api'],
    'namespace' => 'Api',
    'prefix' => 'auth',

], function ($router) {
    Route::group(['namespace' => 'Planner',], function () {
        Route::get('plannerphoto/{planner_id}', [AuthController::class, 'getPlannerPhoto']);
    });
});
Route::group([
    'middleware' => ['api'],
    'namespace' => 'Api',
    'prefix' => 'plan',
], function ($router) {
    Route::group([], function () {
        Route::get('planphoto/{plan_id}/{photo_id}', [PlannerController::class, 'getPlanPhoto']);
    });
});
Route::any('{url}',function (){
    return response()->json('this url not found', 401);
})->where('url','.*')->middleware('api');
