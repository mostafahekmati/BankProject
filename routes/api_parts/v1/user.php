<?php


use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Support\Facades\Route;



Route::controller( UserController::class )->prefix( 'v1' )->group( function () {

    Route::group([
        'prefix' => 'user'
    ],function (){
        Route::get('/top-users', 'topUsers');
    });

} );

