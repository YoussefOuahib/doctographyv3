<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\RegisteredUserController;
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

// Route::post('register', [RegisteredUserController::class, 'store']);

Route::group(['middleware' => 'guest'], function () {

Route::post('login', [
    \App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'store']);
    
Route::post('logout', [
    \App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'destroy']);

});
Route::view('/{any?}', 'dashboard')
    ->name('dashboard')
    ->where('any', '.*');
