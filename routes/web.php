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

Route::get('/', function(){
    return view('index');
});

Route::get('/upload', function(){
    return view('upload');
})->name('upload.index');

Route::get('/upload/validate', function(){
    return view('upload');
})->name('upload.validate');

Route::post('/upload/validate', [\App\Http\Controllers\UploadController::class, 'validateData'])->name('upload.validate');