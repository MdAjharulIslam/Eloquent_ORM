<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ContactController;

Route::get('/', function () {
    return view('welcome');
    
});

Route::resource('student', StudentController::class);

Route::get('/contact',[ContactController::class, 'show']);