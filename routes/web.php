<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/getToken',[\App\Http\Controllers\TiktokController::class,'getToken']);
Route::get('/callBack',[\App\Http\Controllers\TiktokController::class,'getTokenAuth']);
Route::get('/Shops',[\App\Http\Controllers\TiktokController::class,'showShops']);
Route::get('/Form',[\App\Http\Controllers\TiktokController::class,'form']);
Route::post('/Create',[\App\Http\Controllers\TiktokController::class,'createProduct'])->name('createProduct');
