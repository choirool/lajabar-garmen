<?php

use App\Http\Livewire\Users\CreateUsers;
use App\Http\Livewire\Users\UpdateUsers;
use App\Http\Livewire\Users\Users;
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
    return view('welcome');
});

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::prefix('/master-data')->name('master-data.')->group(function () {
        Route::prefix('/users')->group(function () {
            Route::get('/', Users::class)->name('users');
            Route::get('/create-user', CreateUsers::class)->name('create-user');
            Route::get('/update-user/{id}', UpdateUsers::class)->name('update-user');
        });
    });
});
