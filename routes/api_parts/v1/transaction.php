<?php

use App\Http\Controllers\Api\V1\TransactionController;
use Illuminate\Support\Facades\Route;



Route::controller( TransactionController::class )->prefix( 'v1' )->group( function () {

    Route::group([
        'prefix' => 'transaction'
    ],function (){
        Route::post('/transfer', 'transfer');
    });

} );

