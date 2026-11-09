<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');
Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'show'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:6,1')->name('login.store');
});
Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [TicketController::class, 'dashboard'])->name('dashboard');
    Route::get('/customers', [TicketController::class, 'customers'])->name('customers.index');
    Route::resource('tickets', TicketController::class)->only(['index', 'create', 'store', 'show', 'update']);
});
