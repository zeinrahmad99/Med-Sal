<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AppointmentController;


Route::get('/appointments',[AppointmentController::class,'index']);
Route::post('/appointment', [AppointmentController::class, 'store']);
Route::put('/appointment/{id}', [AppointmentController::class, 'update']);
Route::get('/appointment/{id}',[AppointmentController::class,'show']);
Route::delete('/appointment/{id}', [AppointmentController::class, 'delete']);
Route::put('/appointment/done/{id}', [AppointmentController::class, 'doneAppointment']);
Route::put('/appointment/cancel/{id}', [AppointmentController::class, 'cancelAppointment']);



