<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return response()->json([ 'message' => 'Please contact the Administrator to access the Api...',
        'email ' => 'g13.indra@gmail.com']);
})->name('welcome');
