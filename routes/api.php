<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdministrasiController;
use App\Http\Controllers\DokterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LaporanAdmController;
use App\Http\Controllers\ObatController;
use App\Http\Controllers\PasienController;
use App\Http\Controllers\PoliController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\RegistrasiPasienController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;


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

 Route::post('/register', [AuthController::class, 'register']);
 Route::post('/login', [AuthController::class, 'login']);
 
 Route::middleware('auth:sanctum')->group(function () {
     Route::post('/logout', [AuthController::class, 'logout']);
     Route::get('/me', [AuthController::class, 'me']);
 });
 


Route::middleware('auth:sanctum')->group(function () {
    // Routes untuk Obat
    Route::get('/obat', [ObatController::class, 'index']);
    Route::post('/obat', [ObatController::class, 'store']);
    Route::get('/obat/{id}', [ObatController::class, 'show']);
    Route::put('/obat/{id}', [ObatController::class, 'update']);
    Route::delete('/obat/{id}', [ObatController::class, 'destroy']);

    // Routes untuk Pasien
    Route::get('/pasien', [PasienController::class, 'index']);
    Route::post('/pasien', [PasienController::class, 'store']);
    Route::get('/pasien/{id}', [PasienController::class, 'show']);
    Route::put('/pasien/{id}', [PasienController::class, 'update']);
    Route::delete('/pasien/{id}', [PasienController::class, 'destroy']);

    // Routes untuk User
    Route::get('/user', [UserController::class, 'index']);
    Route::post('/user', [UserController::class, 'store']);
    Route::get('/user/{id}', [UserController::class, 'show']);
    Route::put('/user/{id}', [UserController::class, 'update']);
    Route::delete('/user/{id}', [UserController::class, 'destroy']);

    // Routes untuk Dokter
    Route::middleware('auth:sanctum')->get('/dokter', [DokterController::class, 'index']);
    Route::post('/dokter', [DokterController::class, 'store']);
    Route::get('/dokter/{id}', [DokterController::class, 'show']);
    Route::put('/dokter/{id}', [DokterController::class, 'update']);
    Route::delete('/dokter/{id}', [DokterController::class, 'destroy']);

    // Routes untuk Poli
    Route::get('/poli', [PoliController::class, 'index']);
    Route::post('/poli', [PoliController::class, 'store']);
    Route::get('/poli/{id}', [PoliController::class, 'show']);
    Route::put('/poli/{id}', [PoliController::class, 'update']);
    Route::delete('/poli/{id}', [PoliController::class, 'destroy']);

    // Routes untuk Administrasi
    Route::get('/administrasi', [AdministrasiController::class, 'index']);
    Route::post('/administrasi', [AdministrasiController::class, 'store']);
    Route::get('/administrasi/{id}', [AdministrasiController::class, 'show']);
    Route::put('/administrasi/{id}', [AdministrasiController::class, 'update']);
    Route::delete('/administrasi/{id}', [AdministrasiController::class, 'destroy']);

    Route::get('/laporanadm', [LaporanAdmController::class, 'index']);
});