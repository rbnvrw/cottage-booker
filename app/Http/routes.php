<?php

/**
 * Index
 */
Route::get('/', function () {
    return view('index');
});

Route::resource('booking', 'BookingController');
