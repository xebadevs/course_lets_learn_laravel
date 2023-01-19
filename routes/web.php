<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExampleController;
use App\Http\Controllers\UserController;

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

Route::get('/', [ExampleController::class, "homePage"]);

Route::get('/about', [ExampleController::class, "aboutPage"]);

Route::post('/register', [UserController::class, "register"]);
Route::post('/login', [UserController::class, "login"]);