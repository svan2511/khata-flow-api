<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/download/apk', function () {
    $file = public_path('apk/app.apk');
    return response()->download($file);
});
