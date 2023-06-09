<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/createTinyURL', [App\Http\Controllers\UrlHashController::class, 'createTinyURL']);
Route::get('/getOriginalURL', [App\Http\Controllers\UrlHashController::class, 'getOriginalURL']);
Route::get('/updateClickCount', [App\Http\Controllers\UrlHashController::class, 'updateClickCount']);
Route::get('/redirectOriginalUrl/', [App\Http\Controllers\UrlHashController::class, 'redirectOriginalUrl']);
