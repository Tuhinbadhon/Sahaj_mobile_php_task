<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [CustomerController::class, 'index'])->name('dashboard');
Route::get('/export-csv', [CustomerController::class, 'exportCSV'])->name('export.csv');
