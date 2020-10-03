<?php

use App\Http\Livewire\Color\Colors;
use App\Http\Livewire\Color\CreateColor;
use App\Http\Livewire\Color\UpdateColor;
use App\Http\Livewire\Users\CreateUsers;
use App\Http\Livewire\Users\UpdateUsers;
use App\Http\Livewire\Users\Users;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('/', function () {
        return view('dashboard');
    });

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::prefix('/master-data')->name('master-data.')->group(function () {
        Route::prefix('/users')->group(function () {
            Route::get('/', Users::class)->name('users');
            Route::get('/create-user', CreateUsers::class)->name('create-user');
            Route::get('/update-user/{id}', UpdateUsers::class)->name('update-user');
        });

        Route::prefix('colors')->group(function () {
            Route::get('/', Colors::class)->name('colors');
            Route::get('/create-color', CreateColor::class)->name('create-color');
            Route::get('/update-color/{id}', UpdateColor::class)->name('update-color');
        });
    });
});
