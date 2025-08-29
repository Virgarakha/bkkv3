<?php

use App\Http\Controllers\ALUMNI_ROLE\LowonganController as ALUMNI_ROLELowonganController;
use App\Http\Controllers\ALUMNI_ROLE\SurveyController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\BK_ROLE\AlumniController;
use App\Http\Controllers\BK_ROLE\LamaranController;
use App\Http\Controllers\BK_ROLE\LowonganController;
use App\Http\Controllers\BK_ROLE\PerusahaanController;
use App\Http\Controllers\GrafikController;
use App\Http\Controllers\PERUSAHAAN_ROLE\LowonganController as PERUSAHAAN_ROLELowonganController;
use App\Models\Alumni;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->group(function(){
    Route::prefix('auth')->group(function(){
        Route::post('signin', [AuthController::class, 'signin']);
        Route::post('signout', [AuthController::class, 'signout']);
    });

    Route::prefix('bkk')->group(function(){
        Route::post('importCsv', [AlumniController::class, 'import']);
        Route::resource('alumni', AlumniController::class);
        Route::resource('perusahaan', PerusahaanController::class);
        Route::resource('lowongan', LowonganController::class);
        Route::put('verifikasi/lowongan/{id}', [LowonganController::class, 'verifikasi']);
        Route::get('manage/lowongan', [LowonganController::class, 'manage']);
        Route::resource('lamaran', LamaranController::class);
        Route::get('manage/lamaran', [LamaranController::class, 'lamaranFilter']);
    });

    Route::prefix('perusahaan')->group(function(){
        Route::resource('lowongan', PERUSAHAAN_ROLELowonganController::class);
        Route::put('/setstatus/lowongan/{id}', [PERUSAHAAN_ROLELowonganController::class, 'editStatus']);
        Route::put('/setstatus/lamaran/{id}', [PERUSAHAAN_ROLELowonganController::class, 'accAlumni']);
    });

    Route::prefix('alumni')->group(function(){
        Route::put('survey', [SurveyController::class, 'store']);
        Route::resource('lowongan', ALUMNI_ROLELowonganController::class);
    });

    Route::prefix('grafik')->group(function(){
        Route::get('PersentaseStatus', [GrafikController::class, 'PersentaseStatus']);
        Route::get('PersentaseKesesuainJurusan', [GrafikController::class, 'PersentaseKesesuainJurusan']);
        Route::get('PersentasePendaftarTerbanyak', [GrafikController::class, 'PersentasePendaftarTerbanyak']);
    });
});
