<?php

use App\Http\Controllers\API\Auth\AuthController;
use App\Http\Controllers\API\Item\ItemController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

//* auth
Route::post('/login', [AuthController::class, 'login'])->name('users.login');
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('users.logout');
    Route::post('register', [AuthController::class, 'register'])->name('users.register');
    Route::get('users', [AuthController::class, 'getListUsers'])->name('users.get_list_users');
    Route::get('logged-user', [AuthController::class, 'getLoggedUser'])->name('users.get_logged_user');
});

//* items
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('items', [ItemController::class, 'getListItems'])->name('items.index');
    Route::get('items/{item_id}', [ItemController::class, 'getDetailItem'])->name('items.show');
    Route::post('items', [ItemController::class, 'storeItem'])->name('items.store');
    Route::post('items/{item_id}', [ItemController::class, 'updateItem'])->name('items.update');
    Route::post('items/{item_id}/delete', [ItemController::class, 'softDeleteItem'])->name('items.delete');
});
