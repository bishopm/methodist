<?php

use Illuminate\Support\Facades\Route;
use Spatie\Honeypot\ProtectAgainstSpam;

// Website routes
Route::middleware(['web'])->controller('\Bishopm\Methodist\Http\Controllers\HomeController')->group(function () {
    Route::get('/', 'home')->name('home');
    if (substr(url()->current(), strrpos(url()->current(), '/' )+1)<>"admin"){
        Route::get('/{page}', 'page')->name('web.page');
    }
    Route::get('/admin/reports/plan/edit/{id}', ['uses'=>'\Bishopm\Methodist\Http\Controllers\HomeController@editplan','as' => 'admin.plan.edit']);
    Route::get('/plan/{id}/{plandate}', ['uses'=>'\Bishopm\Methodist\Http\Controllers\HomeController@pdf','as' => 'reports.plan']);
});


