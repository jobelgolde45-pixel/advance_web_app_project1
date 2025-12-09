<?php

use App\Http\Controllers\Api\ValidateController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth/register');
});
Route::get('/login-page', function (){
    return view('auth/login');
});
