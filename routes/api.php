<?php

use App\Http\Controllers\Api\LeadsApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application.
|
*/

// Lead Sources API
Route::get('/leads/sources', [LeadsApiController::class, 'sources']);
Route::get('/leads/users', [LeadsApiController::class, 'users']);
Route::get('/leads/statuses', [LeadsApiController::class, 'statuses']);

// Leads API
Route::apiResource('leads', LeadsApiController::class);
