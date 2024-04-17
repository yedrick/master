<?php

use yedrick\Master\App\Http\Controllers\CustomerController;
use yedrick\Master\App\Http\Controllers\MainController;
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


Route::group(['middleware' => 'auth'], function() {
    // Route::get('model-list/{nodeName}', 'MainController@index');
    Route::get('model-list/{nodeName}',[MainController::class,'modelList'])->name('model.list');
    // modelCreate
    Route::get('model/{nodeName}',[MainController::class,'modelCreate'])->name('model');
    Route::get('model/{nodeName}/{id}', [MainController::class,'edit'])->name('model.edit');

    Route::post('model/store/{nodeName}', [MainController::class,'store'])->name('model.store');
    Route::put('model/update/{nodeName}/{id}',[MainController::class,'update'])->name('model.update');
    Route::get('model/delete/{nodeName}/{id}', [MainController::class,'delete'])->name('model.delete');
    Route::get('traducer', [MainController::class, 'traducer']);
    Route::get('export-node/{nodeName}', [MainController::class,'exportNode'])->name('model.export');

   
});

Route::get('test',[CustomerController::class,'getTest'])->name('test');

require __DIR__.'/auth.php';