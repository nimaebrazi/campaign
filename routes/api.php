<?php


Route::prefix('campaign/')->group(function (){

    Route::post('voucher/use', 'Campaign\UseVoucherController');

});