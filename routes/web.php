<?php

use App\Http\Controllers\ProductionController;
use Illuminate\Http\Request;
use App\Http\Livewire\Item\Items;
use App\Http\Livewire\Size\Sizes;
use App\Http\Livewire\Users\Users;
use App\Http\Livewire\Color\Colors;
use Illuminate\Support\Facades\Route;
use App\Http\Livewire\Item\CreateItem;
use App\Http\Livewire\Item\UpdateItem;
use App\Http\Livewire\Size\CreateSize;
use App\Http\Livewire\Size\UpdateSize;
use Illuminate\Support\Facades\Artisan;
use App\Http\Livewire\Color\CreateColor;
use App\Http\Livewire\Color\UpdateColor;
use App\Http\Livewire\Salesman\Salesmen;
use App\Http\Livewire\Users\CreateUsers;
use App\Http\Livewire\Users\UpdateUsers;
use App\Http\Livewire\Customer\Customers;
use App\Http\Livewire\Material\Materials;
use App\Http\Livewire\Transaction\Orders;
use App\Http\Livewire\Category\Categories;
use App\Http\Livewire\Category\CreateCategory;
use App\Http\Livewire\Category\UpdateCategory;
use App\Http\Livewire\Customer\CreateCustomer;
use App\Http\Livewire\Customer\UpdateCustomer;
use App\Http\Livewire\Material\CreateMaterial;
use App\Http\Livewire\Material\UpdateMaterial;
use App\Http\Livewire\Salesman\CreateSalesman;
use App\Http\Livewire\Salesman\UpdateSalesman;
use App\Http\Livewire\Transaction\CreateOrder;
use App\Http\Livewire\Transaction\UpdateOrder;
use App\Http\Livewire\Customer\ManageProductCustomer;
use App\Http\Livewire\Customer\ManageProductCustomerV2;

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
            Route::get('/update-customer/{id}/manage-product-v2', ManageProductCustomerV2::class)->name('manage-products-customer-v2');
            Route::get('/customer-items', \App\Http\Controllers\CustomerItemsController::class)->name('customer.customer-items');
        });

        Route::prefix('items')->group(function () {
            Route::get('/', Items::class)->name('items');
            Route::get('/create-item', CreateItem::class)->name('create-item');
            Route::get('/update-item/{id}', UpdateItem::class)->name('update-item');
        });

        Route::prefix('salesmen')->group(function () {
            Route::get('/', Salesmen::class)->name('salesmen');
            Route::get('/create-salesman', CreateSalesman::class)->name('create-salesman');
            Route::get('/update-salesman/{id}', UpdateSalesman::class)->name('update-salesman');
        });
    });

    Route::prefix('transactions')->name('transactions.')->group(function () {
        Route::get('/orders', Orders::class)->name('orders');
        Route::get('/create-order', CreateOrder::class)->name('create-order');
        Route::get('/update-order/{id}', UpdateOrder::class)->name('update-order');

        Route::prefix('productions')->name('production.')->group(function () {
            Route::get('/{orderId}', [ProductionController::class, 'index'])->name('index');
            Route::post('/{orderId}', [ProductionController::class, 'store'])->name('store');
        });

        Route::prefix('/v2')->name('v2.')->group(function () {
            Route::get('/create-order', [\App\Http\Controllers\Order\OrderV2Controller::class, 'create'])->name('create-order');
            Route::post('/create-order', [\App\Http\Controllers\Order\OrderV2Controller::class, 'store'])->name('store-order');
            Route::get('/update-order/{id}', [\App\Http\Controllers\Order\OrderV2Controller::class, 'edit'])->name('edit-order');
            Route::match(['post', 'patch'], '/update-order', [\App\Http\Controllers\Order\OrderV2Controller::class, 'update'])->name('update-order');
        });

        Route::prefix('/v3')->name('v3.')->group(function () {
            Route::get('/create-order', [\App\Http\Controllers\Order\OrderV3Controller::class, 'create'])->name('create-order');
            Route::get('/update-order/{id}', [\App\Http\Controllers\Order\OrderV3Controller::class, 'edit'])->name('edit-order');
            // Route::post('/create-order', [\App\Http\Controllers\OrderV3Controller::class, 'store'])->name('store-order');
            // Route::patch('/update-order', [\App\Http\Controllers\OrderV3Controller::class, 'update'])->name('update-order');
        });
    });

    Route::get('/artisan', function (Request $request) {
        if ($request->has('c')) {
            Artisan::call($request->c);
        }
    });

    Route::get('/create-symlink', function () {
        $target  = ' /home/u1045124/public_html/staging/storage/app/public';
        $link    = '/home/u1045124/public_html/staging/storage';

        symlink($target, $link);
    });
});
