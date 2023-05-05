<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\InteractionsController;
use App\Http\Controllers\Api\User\UserAuthController;
use App\Http\Controllers\Api\User\UserController;
use App\Http\Controllers\AuthwController;
use App\Http\Controllers\hallsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::group([
    'middleware' => ['api'],
    'prefix' => 'auth',
    'namespace'=>'Api',

], function ($router) {
    Route::group(['namespace'=>'User',],function (){
            Route::post('loginUser', [UserAuthController::class, 'login'])->name('login-user');
            Route::post('registerUser', [UserAuthController::class, 'userRegister']);
            Route::get('userProfile', [UserAuthController::class, 'userProfile']);
            Route::post('updateUser/{user_id}', [UserAuthController::class, 'updateUser']);
            Route::post('logoutUser',[ UserAuthController::class,'logout'])-> middleware(['auth.guard:user-api']);
            Route::get('userphoto/{user_id}', [UserAuthController::class, 'getUserPhoto']);
            Route::get('/getHall/{hall_id}', [UserController::class, 'gethall']);
            Route::get('/getAllHalls', [UserController::class, 'getAllHalls']);



            Route::post('halls/{hall_id}/addLike', [ InteractionsController::class,'addLike']);
            Route::post('halls/{hall_id}/addComment', [ InteractionsController::class,'addComment']);
            Route::get('halls/{hall_id}/getComment', [ InteractionsController::class,'getComment']);
            Route::post('halls/{comment_id}/updateComment', [ InteractionsController::class,'updateComment']);
            Route::delete('halls/{comment_id}/deleteComment', [ InteractionsController::class,'deleteComment']);
            Route::get('halls/{hall_id}/getHallscount', [ hallsController::class,'getHallscount']);
        });


        });
