<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Equipo\EquipoController;

//------------------LOGIN------------------//
// Redirigir la raíz al login si no está autenticado, o al dashboard si ya lo está
Route::get('/', function () {
    return redirect()->route('login');
});

// Rutas para usuarios invitados (No autenticados)
Route::get('/login', [AuthController::class, 'show'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// Rutas protegidas del sistema (Requieren iniciar sesión)
Route::middleware('auth')->group(function () {
    
// Dashboard principal
Route::get('/dashboard', function () { return view('dashboard');})->name('dashboard');

// Cerrar sesión
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

});

//------------------------------------------//

//------------------EQUIPO------------------//
Route::get('/equipos', [App\Http\Controllers\Equipo\EquipoController::class, 'index'])->name('equipos.index');
Route::get('/equipos/create', [App\Http\Controllers\Equipo\EquipoController::class, 'create'])->name('equipos.create');
Route::post('/equipos', [App\Http\Controllers\Equipo\EquipoController::class, 'store'])->name('equipos.store');
Route::get('/equipos/{id}/edit', [App\Http\Controllers\Equipo\EquipoController::class, 'edit'])->name('equipos.edit');
Route::put('/equipos/{id}', [App\Http\Controllers\Equipo\EquipoController::class, 'update'])->name('equipos.update');
Route::delete('/equipos/{id}', [App\Http\Controllers\Equipo\EquipoController::class, 'destroy'])->name('equipos.destroy');
