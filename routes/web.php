<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


Route::prefix('users')->name('users.')->group(function () {
    Route::controller(UsersController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::post('/bulkaktif', 'bulkaktif')->name('bulkaktif');
        Route::post('/bulknonaktif', 'bulknonaktif')->name('bulknonaktif');
        Route::get('/create', 'create')->name('create');
        Route::delete('/bulkdelete', 'bulkdelete')->name('bulkdelete');
        Route::get('/{id}', 'show')->name('show');
        Route::put('/{id}', 'update')->name('update');
        Route::delete('/{id}', 'destroy')->name('destroy');
        Route::get('/{id}/edit', 'edit')->name('edit');
    });
});
    