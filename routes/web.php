<?php

use App\Http\Livewire\Category\Categories;
use App\Http\Livewire\Category\CreateCategory;
use App\Http\Livewire\Category\UpdateCategory;
use App\Http\Livewire\Color\Colors;
use App\Http\Livewire\Color\CreateColor;
use App\Http\Livewire\Color\UpdateColor;
use App\Http\Livewire\Customer\CreateCustomer;
use App\Http\Livewire\Customer\Customers;
use App\Http\Livewire\Customer\ManageProductCustomer;
use App\Http\Livewire\Customer\UpdateCustomer;
use App\Http\Livewire\Item\CreateItem;
use App\Http\Livewire\Item\Items;
use App\Http\Livewire\Item\UpdateItem;
use App\Http\Livewire\Material\CreateMaterial;
use App\Http\Livewire\Material\Materials;
use App\Http\Livewire\Material\UpdateMaterial;
use App\Http\Livewire\Size\CreateSize;
use App\Http\Livewire\Size\Sizes;
use App\Http\Livewire\Size\UpdateSize;
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

        Route::prefix('sizes')->group(function () {
            Route::get('/', Sizes::class)->name('sizes');
            Route::get('/create-size', CreateSize::class)->name('create-size');
            Route::get('/update-size/{id}', UpdateSize::class)->name('update-size');
        });

        Route::prefix('materials')->group(function () {
            Route::get('/', Materials::class)->name('materials');
            Route::get('/create-material', CreateMaterial::class)->name('create-material');
            Route::get('/update-material/{id}', UpdateMaterial::class)->name('update-material');
        });

        Route::prefix('categories')->group(function () {
            Route::get('/', Categories::class)->name('categories');
            Route::get('/create-category', CreateCategory::class)->name('create-category');
            Route::get('/update-category/{id}', UpdateCategory::class)->name('update-category');
        });

        Route::prefix('customers')->group(function () {
            Route::get('/', Customers::class)->name('customers');
            Route::get('/create-customer', CreateCustomer::class)->name('create-customer');
            Route::get('/update-customer/{id}', UpdateCustomer::class)->name('update-customer');
            Route::get('/update-customer/{id}/manage-product', ManageProductCustomer::class)->name('manage-products-customer');
        });

        Route::prefix('items')->group(function () {
            Route::get('/', Items::class)->name('items');
            Route::get('/create-item', CreateItem::class)->name('create-item');
            Route::get('/update-item/{id}', UpdateItem::class)->name('update-item');
        });
    });
});
