<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Equipo\EquipoController;
use App\Http\Controllers\Empleado\EmpleadoController;

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

//------------------EMPLEADO------------------//
Route::get('/empleados', [App\Http\Controllers\Empleado\EmpleadoController::class, 'index'])->name('empleados.index');
Route::get('/empleados/create', [App\Http\Controllers\Empleado\EmpleadoController::class, 'create'])->name('empleados.create');
Route::post('/empleados', [App\Http\Controllers\Empleado\EmpleadoController::class, 'store'])->name('empleados.store');
Route::get('/empleados/{id}/edit', [App\Http\Controllers\Empleado\EmpleadoController::class, 'edit'])->name('empleados.edit');
Route::put('/empleados/{id}', [App\Http\Controllers\Empleado\EmpleadoController::class, 'update'])->name('empleados.update');
Route::delete('/empleados/{id}', [App\Http\Controllers\Empleado\EmpleadoController::class, 'destroy'])->name('empleados.destroy');

//------------------TRASLADOS------------------//
Route::get('/traslados', [App\Http\Controllers\Traslado\TrasladoController::class, 'index'])->name('traslados.index');
Route::get('/traslados/create', [App\Http\Controllers\Traslado\TrasladoController::class, 'create'])->name('traslados.create');
Route::post('/traslados', [App\Http\Controllers\Traslado\TrasladoController::class, 'store'])->name('traslados.store');
Route::get('/traslados/{id}/edit', [App\Http\Controllers\Traslado\TrasladoController::class, 'edit'])->name('traslados.edit');
Route::put('/traslados/{id}', [App\Http\Controllers\Traslado\TrasladoController::class, 'update'])->name('traslados.update');
Route::delete('/traslados/{id}', [App\Http\Controllers\Traslado\TrasladoController::class, 'destroy'])->name('traslados.destroy');
