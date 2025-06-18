<?php

use App\Http\Controllers\CompanyController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Rutas del recurso Company
Route::apiResource('companies', CompanyController::class);

// Rutas de búsqueda específicas por POST
Route::post('companies/search/ruc', [CompanyController::class, 'searchByRuc']);
Route::post('companies/search/name', [CompanyController::class, 'searchByName']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
