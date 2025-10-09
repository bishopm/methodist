<?php

use Illuminate\Support\Facades\Route;
use Spatie\Honeypot\ProtectAgainstSpam;

// PWA Manifest and SW

Route::get('/manifest.json', fn() => response()->view('methodist::pwa.manifest')->header('Content-Type', 'application/json'));
Route::get('/service-worker.js', fn () => response()->view('methodist::pwa.service-worker')->header('Content-Type', 'application/javascript'));

// Website routes
Route::middleware(['web'])->controller('\Bishopm\Methodist\Http\Controllers\HomeController')->group(function () {
    Route::get('/', 'home')->name('home');
    Route::get('/ideas', 'ideas')->name('ideas');
    Route::post('/ideas/store', 'storeidea')->name('ideas.store');
    Route::get('/lectionary/{sunday?}','lectionary')->name('lectionary');
    Route::get('/ministers/{id}','minister')->name('minister');
    Route::get('/offline', 'offline')->name('offline');
    Route::get('/admin/reports/plan/edit/{id}', ['uses'=>'\Bishopm\Methodist\Http\Controllers\HomeController@editplan','as' => 'admin.plan.edit']);
    Route::get('/plan/{id}/{plandate}', ['uses'=>'\Bishopm\Methodist\Http\Controllers\HomeController@pdf','as' => 'reports.plan']);
    if (!str_contains(url()->current(),"admin")){
        Route::get('/{district}', 'district')->name('district');
        Route::get('/{district}/{circuit}', 'circuit')->name('circuit');
        Route::get('/{district}/{circuit}/{society}', 'society')->name('society');
    }
});

