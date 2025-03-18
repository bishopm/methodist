<?php

use Illuminate\Support\Facades\Route;
use Spatie\Honeypot\ProtectAgainstSpam;

// Website routes
Route::middleware(['web'])->controller('\Bishopm\Methodist\Http\Controllers\HomeController')->group(function () {
    Route::get('/', 'home')->name('home');
    if (substr(url()->current(), strrpos(url()->current(), '/' )+1)<>"admin"){
        Route::get('/{page}', 'page')->name('web.page');
    }
});


