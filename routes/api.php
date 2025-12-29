<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Item\ItemController;
use App\Http\Controllers\Api\Loan\LoanController;
use App\Http\Controllers\Api\User\UserController;

Route::get('/user', [UserController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/items', [ItemController::class, 'index']);
    Route::post('/item/store', [ItemController::class, 'store']);
    Route::put('/item/update/{item}', [ItemController::class, 'update']);
    Route::delete('/item/delete/{item}', [ItemController::class, 'destroy']);

    Route::get('/loans', [LoanController::class, 'index']);
    Route::post('/loan/store', [LoanController::class, 'store']);
    Route::get('/loan/{loan}', [LoanController::class, 'show']);
    Route::put('/loan/update/{loan}', [LoanController::class, 'update']);
    Route::delete('/loan/delete/{loan}', [LoanController::class, 'destroy']);

    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::post('/login', [AuthController::class, 'login']);
