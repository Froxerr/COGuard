<?php

use App\Http\Controllers\IndexController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\PanelController;
use App\Http\Controllers\MeasurementController;
use Illuminate\Support\Facades\Route;

// Auth Routes
Route::get('/',[IndexController::class,'index']);
Route::get('/login',[LoginController::class,'index'])->name('login');
Route::post('/login',[LoginController::class,'login']);
Route::get('/register',[RegisterController::class,'index']);
Route::post('/register',[RegisterController::class,'store']);
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

// Protected Routes
Route::get('/panel', [PanelController::class, 'systemStatus'])->name('system-status');
Route::get('/panel/alarm-control', [PanelController::class, 'alarmControl'])->name('panel.alarm-control');
Route::get('/panel/limits-control', [PanelController::class, 'limitsControl'])->name('panel.alarm-limits-control');
Route::get('/panel/sensors-settings', [PanelController::class, 'sensorsSettings'])->name('panel.sensors-settings');
Route::get('/panel/alarms-history', [PanelController::class, 'alarmsHistory'])->name('panel.alarms-history');
// Ölçüm routes
Route::get('/panel/measurements-history', [MeasurementController::class, 'index'])->name('panel.measurements-history');
Route::get('/panel/measurements-history/data', [MeasurementController::class, 'getMeasurements']);
