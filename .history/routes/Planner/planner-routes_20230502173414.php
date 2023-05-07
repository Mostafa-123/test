<?php
use App\Http\Controllers;
use App\Http\Controllers\Api\Planner\PlannerAuthController ;
use App\Http\Controllers\Api\Planner\PlannerController ;
use Illuminate\Support\Facades\Route;
Route::group([
    'middleware' => ['api'],
    'prefix' => 'auth',
    'namespace'=>'Api',

], function ($router) {
    Route::group(['namespace'=>'Planner',],function (){
            Route::post('loginPlanner', [PlannerAuthController::class, 'login'])->name('login-Planner');
            Route::post('registerPlanner', [PlannerAuthController::class, 'registerPlanner']);
            Route::get('profilePlanner', [PlannerAuthController::class, 'plannerProfile']);
            Route::post('updatePlanner/{planner_id}', [PlannerAuthController::class, 'updatePlanner']);
            //5555555555
            Route::post('logoutPlanner',[ PlannerAuthController::class,'logout'])-> middleware(['auth.guard:planner-api']);
                });


        });
Route::group([
    'middleware' => ['api'],
    'namespace' => 'Api',

], function ($router) {
    Route::group(['namespace' => 'Planner',], function () {
        Route::get('plannerphoto/{planner_id}', [PlannerAuthController::class, 'getPlannerPhoto']);
        Route::post('addPlan', [PlannerController::class, 'addPlan']);
        Route::get('planphoto/{plan_id}/{photo_id}', [PlannerController::class, 'getPlanPhoto']);
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
        Route::post('rejectBooking/{bookingplanId}', [PlannerController::class, 'rejectBooking']);

    });
});

// Route::any('{url}',function (){
//     return $this->response("","this url not found",401);
// })->where('url','.*')->middleware('api');
