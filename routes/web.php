<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/download/apk', function () {
    return redirect('https://github.com/svan2511/khataflow/releases/download/v1.0.0/app.apk');
});
