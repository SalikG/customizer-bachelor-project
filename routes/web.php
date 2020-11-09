<?php

use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

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
    return view('app');
});

//USER AUTH
Route::post('/auth/register', [UserController::class, 'register']);
Route::post('/auth/login', [UserController::class, 'login']);
Route::post('/auth/logout', [UserController::class, 'logout']);



// FILE HANDLING
Route::middleware('auth:sanctum')->post('/file-upload/temporary-3d-model-single-file', [FileUploadController::class, 'temporary3dModelSingeFile']);
