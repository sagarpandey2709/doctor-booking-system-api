<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Doctor routes
Route::middleware(['auth:sanctum', 'adminMiddleware:1'])->group(function () {
    Route::get('/doctor/appointments', [AppointmentController::class, 'doctorAppointments']);
    Route::put('/doctor/appointments/{id}', [AppointmentController::class, 'updateDoctorAppointmentStatus']);
});

// Patient routes
Route::middleware(['auth:sanctum', 'adminMiddleware:0'])->group(function () {
    Route::post('/patient/appointments', [AppointmentController::class, 'createAppointment']);
    Route::put('/patient/appointments/{id}', [AppointmentController::class, 'updateAppointmentStatus']);
    Route::get('/patient/appointments', [AppointmentController::class, 'viewAppointments']);
});

Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/signup',[AuthController::class, 'signup'])->name('signup');
