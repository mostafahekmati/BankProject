<?php

use App\Http\Controllers\Api\V1\TransactionController;
use Illuminate\Support\Facades\Route;



Route::controller( TransactionController::class )->prefix( 'v1' )->group( function () {

    //Knowing that there is an api, I used the prefix so that other apis can be added in the future so that the code is cleaner.
    Route::group([
        'prefix' => 'transaction'
    ],function (){
        Route::post('/transfer', 'transfer');
    });

} );

