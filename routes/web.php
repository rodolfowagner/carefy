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

Route::get('/', [\App\Http\Controllers\IndexController::class, 'index'])->name('home');

Route::get('/upload', function(){
    return view('upload');
})->name('upload.index');

Route::post('upload', [\App\Http\Controllers\UploadController::class, 'upload'])->name('upload.post');
Route::resource('pacientes', \App\Http\Controllers\PatientController::class, ['names' => 'patients'])->parameters(['pacientes' => 'patients']);
Route::resource('internamentos', \App\Http\Controllers\HospitalizationController::class, ['names' => 'hospitalizations'])->parameters(['internamentos' => 'hospitalizations']);
Route::get('delete', [\App\Http\Controllers\UploadController::class, 'deleteAll'])->name('upload.delete');