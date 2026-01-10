<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\EmailVerificationController;
use App\Http\Controllers\Api\Category\CategoriesController;
use App\Http\Controllers\Api\Item\ItemController;
use App\Http\Controllers\Api\Loan\LoanController;
use App\Http\Controllers\Api\User\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/user', [UserController::class, 'index']);

/*
|--------------------------------------------------------------------------
| EMAIL VERIFICATION (PUBLIC - SIGNED)
|--------------------------------------------------------------------------
*/
Route::get('/email/verify/{id}/{hash}',
    [EmailVerificationController::class, 'verify']
)->middleware('signed')
    ->name('verification.verify');

/*
|--------------------------------------------------------------------------
| AUTHENTICATED ROUTES (LOGIN REQUIRED)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    // User info
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Resend verification (khusus login tapi belum verified)
    Route::post('/email/verification-notification',
        [EmailVerificationController::class, 'resend']
    );

    // Items
    Route::get('/items', [ItemController::class, 'index']);
    Route::post('/item/store', [ItemController::class, 'store']);
    Route::put('/item/update/{item}', [ItemController::class, 'update']);
    Route::delete('/item/delete/{item}', [ItemController::class, 'destroy']);

    // Loans
    Route::get('/loans', [LoanController::class, 'index']);
    Route::post('/loan/store', [LoanController::class, 'store']);
    Route::get('/loan/{loan}', [LoanController::class, 'show']);
    Route::put('/loan/update/{loan}', [LoanController::class, 'update']);
    Route::delete('/loan/delete/{loan}', [LoanController::class, 'destroy']);

    // Categories
    Route::get('/categories', [CategoriesController::class, 'index']);
    Route::post('/categories/store', [CategoriesController::class, 'store']);
    Route::put('/categories/update/{category}', [CategoriesController::class, 'update']);
    Route::delete('/categories/delete/{category}', [CategoriesController::class, 'destroy']);

});
