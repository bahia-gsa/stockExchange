<?php

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
use App\Http\Controllers\transactioncontroller;
use App\Http\Controllers\homeController;

Route::get('/', [homeController::class, 'index']);
Route::get('FormBuy', [transactioncontroller::class, 'CreateFormBuy'])->middleware('auth');
Route::get('FormSell', [transactioncontroller::class, 'CreateFormSell'])->middleware('auth');
Route::get('FormSplit', [transactioncontroller::class, 'CreateFormSplit'])->middleware('auth');
Route::get('view_ticker', [transactioncontroller::class, 'openTicker'])->middleware('auth');
Route::get('see_ticker', [transactioncontroller::class, 'showTicker']);
Route::get('dashboardshares', [transactioncontroller::class, 'dashboardshares'])->middleware('auth');
Route::get('delete_ticker', [transactioncontroller::class, 'deletePage'])->middleware('auth');
Route::get('show_transactions', [transactioncontroller::class, 'showTransactions']);
Route::get('/delete/{id}', [TransactionController::class, 'delete'])->middleware('auth');


Route::post('op_buy', [transactioncontroller::class, 'store_buy']);
Route::post('op_sell', [transactioncontroller::class, 'store_sell']);
Route::post('op_split', [transactioncontroller::class, 'store_split']);








Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified'
])->group(function () {
    Route::get('/dashboard', function () {
        return view('welcome'); // mudei o original para ser a minha dashboard
    })->name('dashboard');
});
