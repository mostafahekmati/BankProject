<?php

use App\Http\Controllers\Api\V1\TransactionController;
use Illuminate\Support\Facades\Route;



Route::controller( TransactionController::class )->prefix( 'v1' )->group( function () {

    //با علم به اینکه میدانستم یک api هست بازم از prefix استفاده کردم که در آینده api های دیگر هم اضافه بشه که کد تمیز تر باشه.
    Route::group([
        'prefix' => 'transaction'
    ],function (){
        Route::post('/transfer', 'transfer');
    });

} );

