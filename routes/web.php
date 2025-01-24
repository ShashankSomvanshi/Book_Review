<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AccountController;
use App\Http\Controllers\BookController;

Route::get('/', function () {
    return view('welcome');
});





Route::prefix('account')->group(function () {

    Route::group(['middleware'=>'guest'],function(){
        Route::get('register',[AccountController::class,'register'])->name('account.register');

        Route::post('processRegister',[AccountController::class,'processRegister'])->name('account.processRegister');

        Route::get('login',[AccountController::class,'login'])->name('account.login');

        Route::post('authenticate',[AccountController::class,'authenticate'])->name('account.authenticate');

        
    });

    Route::group(['middleware'=>'auth'],function(){
        Route::get('profile',[AccountController::class,'profile'])->name('account.profile');
        Route::get('logout',[AccountController::class,'logout'])->name('account.logout');
        Route::post('update',[AccountController::class,'updateProfile'])->name('account.updateProfile');

        Route::get('book',[BookController::class,'index'])->name('book.index');
        Route::get('book/create',[BookController::class,'create'])->name('book.create');
        Route::post('book/store',[BookController::class,'store'])->name('book.store');
    });
});

