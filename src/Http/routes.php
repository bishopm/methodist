<?php

use Illuminate\Support\Facades\Route;
use Livewire\Livewire;

Livewire::setUpdateRoute(function ($handle) {
    return Route::post('/custom/livewire/update', $handle)->middleware(['web']);
});


