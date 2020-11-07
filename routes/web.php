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

//USER
Route::post('/auth/register', [UserController::class, 'register']);



// FILE HANDLING
Route::post('/file-upload/temporary-3d-model-single-file', [FileUploadController::class, 'temporary3dModelSingeFile']);
