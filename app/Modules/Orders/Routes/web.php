<?php

use App\Modules\Orders\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::prefix('orders')->group(function () {
    Route::get('/', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/create', [OrderController::class, 'create'])->name('orders.create');
    Route::get('/{order}', [OrderController::class, 'detail'])->name('orders.detail');
    Route::post('/', [OrderController::class, 'store'])->name('orders.store');
    Route::patch('/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::patch('/{order}/deliver', [OrderController::class, 'deliver'])->name('orders.deliver');
    Route::get('/{order}/edit', [OrderController::class, 'edit'])->name('orders.edit');
    Route::patch('/{order}/update', [OrderController::class, 'update'])->name('orders.update');
});
