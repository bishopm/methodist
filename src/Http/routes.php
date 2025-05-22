<?php

use Illuminate\Support\Facades\Route;
use Spatie\Honeypot\ProtectAgainstSpam;

// Website routes
Route::middleware(['web'])->controller('\Bishopm\Methodist\Http\Controllers\HomeController')->group(function () {
    Route::get('/', 'home')->name('home');
    Route::get('/lectionary/{sunday?}','lectionary')->name('lectionary');
    Route::get('/admin/reports/plan/edit/{id}', ['uses'=>'\Bishopm\Methodist\Http\Controllers\HomeController@editplan','as' => 'admin.plan.edit']);
    Route::get('/plan/{id}/{plandate}', ['uses'=>'\Bishopm\Methodist\Http\Controllers\HomeController@pdf','as' => 'reports.plan']);
    if (!str_contains(url()->current(),"admin")){
        Route::get('/{district}', 'district')->name('district');
        Route::get('/{district}/{circuit}', 'circuit')->name('circuit');
        Route::get('/{district}/{circuit}/{society}', 'society')->name('society');
    }
});


