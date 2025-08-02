<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SalesController;
use App\Http\Controllers\Api\SystemController;
use App\Http\Controllers\Api\TrashController;
use App\Http\Controllers\Api\AuthController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Authentication
Route::post('login', [AuthController::class, 'login']);
Route::get('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::get('sales-list/{paginate}/{customerId}/{productId}/{startDate}/{endDate}', [SalesController::class, 'list'])->middleware('auth:sanctum');
Route::post('create-sales', [SalesController::class, 'create'])->middleware('auth:sanctum');
Route::delete('delete-sale/{id}', [SalesController::class, 'destroy'])->middleware('auth:sanctum');

Route::get('customers', [SystemController::class, 'customers'])->middleware('auth:sanctum');
Route::post('products', [SystemController::class, 'products'])->middleware('auth:sanctum');

Route::get('trash-list/{paginate}', [TrashController::class, 'list'])->middleware('auth:sanctum');
Route::delete('delete-trash-item/{id}', [TrashController::class, 'forceDelete'])->middleware('auth:sanctum');
Route::post('restore-trash-item', [TrashController::class, 'restore'])->middleware('auth:sanctum');
