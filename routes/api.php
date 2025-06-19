<?php

use Illuminate\Support\Facades\Route;

Route::post('/{service}/{query}/{state?}/{closest?}', 'Controller@query')->name('query');

Route::get('/{service}/{state}/{closest?}', 'Controller@state')->name('state');
