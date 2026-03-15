<?php

use App\Http\Controllers\BillController;
use App\Http\Controllers\CashpowerController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\MeterController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Language switch
Route::get('/locale/{locale}', [LocaleController::class, 'switch'])->name('locale.switch');

// Landing page
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Contracts
    Route::resource('contracts', ContractController::class);

    // Meters
    Route::get('/contracts/{contract}/meters/create', [MeterController::class, 'create'])->name('meters.create');
    Route::post('/contracts/{contract}/meters', [MeterController::class, 'store'])->name('meters.store');
    Route::get('/meters/{meter}', [MeterController::class, 'show'])->name('meters.show');
    Route::delete('/meters/{meter}', [MeterController::class, 'destroy'])->name('meters.destroy');
    Route::get('/api/amperage-options', [MeterController::class, 'amperageOptions'])->name('api.amperage_options');

    // Bills
    Route::get('/bills', [BillController::class, 'index'])->name('bills.index');
    Route::get('/bills/{bill}', [BillController::class, 'show'])->name('bills.show');
    Route::post('/bills/{bill}/pay', [BillController::class, 'pay'])->name('bills.pay');

    // CashPower
    Route::get('/cashpower', [CashpowerController::class, 'index'])->name('cashpower.index');
    Route::post('/cashpower/purchase', [CashpowerController::class, 'purchase'])->name('cashpower.purchase');
    Route::get('/cashpower/{meter}/history', [CashpowerController::class, 'history'])->name('cashpower.history');
});

require __DIR__ . '/auth.php';
