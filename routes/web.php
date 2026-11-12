<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Equipo\EquipoController;
use App\Http\Controllers\Empleado\EmpleadoController;
use App\Http\Controllers\Traslado\TrasladoController;
use App\Support\Permisos;

//------------------LOGIN------------------//
// Redirigir la raíz al login si no está autenticado, o al dashboard si ya lo está
Route::get('/', function () {
    return redirect()->route('login');
});

// Rutas para usuarios invitados (No autenticados)
Route::get('/login', [AuthController::class, 'show'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| Rutas protegidas del sistema
|--------------------------------------------------------------------------
| Requieren sesión iniciada. Además cada ruta exige un permiso del rol del usuario
| ("modulo.accion", ver config/permisos.php):
|   ver / crear / editar / eliminar
*/
Route::middleware('auth')->group(function () {

//------------------DASHBOARD / SESIÓN------------------//
// Dashboard principal. Si el rol no lo puede ver, se envía a su primera pantalla permitida.
Route::get('/dashboard', [App\Http\Controllers\Dashboard\DashboardController::class, 'index'])->name('dashboard');

// Cerrar sesión
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

//------------------EQUIPO------------------//
Route::get('/equipos', [App\Http\Controllers\Equipo\EquipoController::class, 'index'])->name('equipos.index')->middleware('permiso:equipos.ver');
Route::get('/equipos/create', [App\Http\Controllers\Equipo\EquipoController::class, 'create'])->name('equipos.create')->middleware('permiso:equipos.crear');
Route::post('/equipos', [App\Http\Controllers\Equipo\EquipoController::class, 'store'])->name('equipos.store')->middleware('permiso:equipos.crear');
Route::get('/equipos/{id}/edit', [App\Http\Controllers\Equipo\EquipoController::class, 'edit'])->name('equipos.edit')->middleware('permiso:equipos.editar');
Route::put('/equipos/{id}', [App\Http\Controllers\Equipo\EquipoController::class, 'update'])->name('equipos.update')->middleware('permiso:equipos.editar');
Route::delete('/equipos/{id}', [App\Http\Controllers\Equipo\EquipoController::class, 'destroy'])->name('equipos.destroy')->middleware('permiso:equipos.eliminar');

//------------------EMPLEADO------------------//
Route::get('/empleados', [App\Http\Controllers\Empleado\EmpleadoController::class, 'index'])->name('empleados.index')->middleware('permiso:empleados.ver');
Route::get('/empleados/create', [App\Http\Controllers\Empleado\EmpleadoController::class, 'create'])->name('empleados.create')->middleware('permiso:empleados.crear');
Route::post('/empleados', [App\Http\Controllers\Empleado\EmpleadoController::class, 'store'])->name('empleados.store')->middleware('permiso:empleados.crear');
Route::get('/empleados/{id}/edit', [App\Http\Controllers\Empleado\EmpleadoController::class, 'edit'])->name('empleados.edit')->middleware('permiso:empleados.editar');
Route::put('/empleados/{id}', [App\Http\Controllers\Empleado\EmpleadoController::class, 'update'])->name('empleados.update')->middleware('permiso:empleados.editar');
Route::delete('/empleados/{id}', [App\Http\Controllers\Empleado\EmpleadoController::class, 'destroy'])->name('empleados.destroy')->middleware('permiso:empleados.eliminar');

//------------------TRASLADOS------------------//
Route::get('/traslados', [App\Http\Controllers\Traslado\TrasladoController::class, 'index'])->name('traslados.index')->middleware('permiso:traslados.ver');
Route::get('/traslados/create', [App\Http\Controllers\Traslado\TrasladoController::class, 'create'])->name('traslados.create')->middleware('permiso:traslados.crear');
Route::post('/traslados', [App\Http\Controllers\Traslado\TrasladoController::class, 'store'])->name('traslados.store')->middleware('permiso:traslados.crear');

// Rutas AJAX de búsqueda (deben ir ANTES de /traslados/{id} para que no las capture el comodín).
// También las usan los formularios de mantenimiento y de baja, por eso basta con cualquiera de esos permisos.
Route::get('/traslados/buscar-equipo', [TrasladoController::class, 'getEquipoByCodigo'])->name('traslados.buscarEquipo')->middleware('permiso:traslados.crear,mantenimientos.crear,bajas.crear');
Route::get('/traslados/buscar-empleado', [TrasladoController::class, 'getEmpleadoByL4'])->name('traslados.buscarEmpleado')->middleware('permiso:traslados.crear,mantenimientos.crear,bajas.crear');

Route::post('/traslados/{id}/devolver', [App\Http\Controllers\Traslado\TrasladoController::class, 'devolver'])->name('traslados.devolver')->middleware('permiso:traslados.editar');
Route::get('/traslados/{id}/imprimir', [App\Http\Controllers\Traslado\TrasladoController::class, 'imprimir'])->name('traslados.imprimir')->middleware('permiso:traslados.ver');
Route::get('/traslados/{id}', [App\Http\Controllers\Traslado\TrasladoController::class, 'show'])->name('traslados.show')->middleware('permiso:traslados.ver');
Route::get('/traslados/{id}/edit', [App\Http\Controllers\Traslado\TrasladoController::class, 'edit'])->name('traslados.edit')->middleware('permiso:traslados.editar');
Route::put('/traslados/{id}', [App\Http\Controllers\Traslado\TrasladoController::class, 'update'])->name('traslados.update')->middleware('permiso:traslados.editar');
Route::delete('/traslados/{id}', [App\Http\Controllers\Traslado\TrasladoController::class, 'destroy'])->name('traslados.destroy')->middleware('permiso:traslados.eliminar');

//------------------DOCUMENTO FIRMADO (traslados y bajas)------------------//
// Ver exige poder ver el módulo. Subir exige "crear" (primera vez) o "editar" (reemplazo); eso lo valida el controlador.
Route::get('/traslados/{id}/documento', [App\Http\Controllers\Documento\DocumentoFirmadoController::class, 'ver'])->defaults('tipo', 'traslados')->name('traslados.documento.ver')->middleware('permiso:traslados.ver');
Route::post('/traslados/{id}/documento', [App\Http\Controllers\Documento\DocumentoFirmadoController::class, 'subir'])->defaults('tipo', 'traslados')->name('traslados.documento.subir')->middleware('permiso:traslados.crear,traslados.editar');
Route::get('/bajas/{id}/documento', [App\Http\Controllers\Documento\DocumentoFirmadoController::class, 'ver'])->defaults('tipo', 'bajas')->name('bajas.documento.ver')->middleware('permiso:bajas.ver');
Route::post('/bajas/{id}/documento', [App\Http\Controllers\Documento\DocumentoFirmadoController::class, 'subir'])->defaults('tipo', 'bajas')->name('bajas.documento.subir')->middleware('permiso:bajas.crear,bajas.editar');

//------------------NOTIFICACIONES------------------//
// Cada usuario solo ve y revisa las suyas (lo valida el controlador)
Route::get('/notificaciones', [App\Http\Controllers\Notificacion\NotificacionController::class, 'index'])->name('notificaciones.index')->middleware('permiso:notificaciones.ver');
Route::post('/notificaciones/revisar-todas', [App\Http\Controllers\Notificacion\NotificacionController::class, 'revisarTodas'])->name('notificaciones.revisarTodas')->middleware('permiso:notificaciones.ver');
Route::post('/notificaciones/{id}/revisar', [App\Http\Controllers\Notificacion\NotificacionController::class, 'revisar'])->name('notificaciones.revisar')->middleware('permiso:notificaciones.ver');

//------------------MANTENIMIENTO------------------//
Route::get('/mantenimientos', [App\Http\Controllers\Mantenimiento\MantenimientoController::class, 'index'])->name('mantenimientos.index')->middleware('permiso:mantenimientos.ver');
Route::get('/mantenimientos/create', [App\Http\Controllers\Mantenimiento\MantenimientoController::class, 'create'])->name('mantenimientos.create')->middleware('permiso:mantenimientos.crear');
Route::post('/mantenimientos', [App\Http\Controllers\Mantenimiento\MantenimientoController::class, 'store'])->name('mantenimientos.store')->middleware('permiso:mantenimientos.crear');
Route::get('/mantenimientos/{id}/imprimir', [App\Http\Controllers\Mantenimiento\MantenimientoController::class, 'imprimir'])->name('mantenimientos.imprimir')->middleware('permiso:mantenimientos.ver');
Route::get('/mantenimientos/{id}', [App\Http\Controllers\Mantenimiento\MantenimientoController::class, 'show'])->name('mantenimientos.show')->middleware('permiso:mantenimientos.ver');
Route::get('/mantenimientos/{id}/edit', [App\Http\Controllers\Mantenimiento\MantenimientoController::class, 'edit'])->name('mantenimientos.edit')->middleware('permiso:mantenimientos.editar');
Route::put('/mantenimientos/{id}', [App\Http\Controllers\Mantenimiento\MantenimientoController::class, 'update'])->name('mantenimientos.update')->middleware('permiso:mantenimientos.editar');
Route::delete('/mantenimientos/{id}', [App\Http\Controllers\Mantenimiento\MantenimientoController::class, 'destroy'])->name('mantenimientos.destroy')->middleware('permiso:mantenimientos.eliminar');

//------------------BAJAS------------------//
Route::get('/bajas', [App\Http\Controllers\Baja\BajaController::class, 'index'])->name('bajas.index')->middleware('permiso:bajas.ver');
Route::get('/bajas/create', [App\Http\Controllers\Baja\BajaController::class, 'create'])->name('bajas.create')->middleware('permiso:bajas.crear');
Route::post('/bajas', [App\Http\Controllers\Baja\BajaController::class, 'store'])->name('bajas.store')->middleware('permiso:bajas.crear');
Route::get('/bajas/{id}/imprimir', [App\Http\Controllers\Baja\BajaController::class, 'imprimir'])->name('bajas.imprimir')->middleware('permiso:bajas.ver');
Route::get('/bajas/{id}', [App\Http\Controllers\Baja\BajaController::class, 'show'])->name('bajas.show')->middleware('permiso:bajas.ver');

//------------------BITÁCORA------------------//
Route::get('/bitacora', [App\Http\Controllers\Bitacora\BitacoraController::class, 'index'])->name('bitacora.index')->middleware('permiso:bitacora.ver');
Route::get('/bitacora/{id}', [App\Http\Controllers\Bitacora\BitacoraController::class, 'show'])->name('bitacora.show')->middleware('permiso:bitacora.ver');

//------------------TARJETA DE RESPONSABILIDAD------------------//
Route::get('/tarjetas', [App\Http\Controllers\Tarjeta\TarjetaController::class, 'index'])->name('tarjetas.index')->middleware('permiso:tarjetas.ver');
Route::get('/tarjetas/{id}/imprimir', [App\Http\Controllers\Tarjeta\TarjetaController::class, 'imprimir'])->name('tarjetas.imprimir')->middleware('permiso:tarjetas.ver');
Route::get('/tarjetas/{id}', [App\Http\Controllers\Tarjeta\TarjetaController::class, 'show'])->name('tarjetas.show')->middleware('permiso:tarjetas.ver');

//------------------REPORTERÍA------------------//
// Solo el Administrador (ningún otro rol tiene "reportes.ver")
Route::prefix('reportes')->name('reportes.')->middleware('permiso:reportes.ver')->group(function () {
    $c = App\Http\Controllers\Reporte\ReporteController::class;

    Route::get('/', [$c, 'index'])->name('index');

    Route::get('/bajas-equipos', [$c, 'bajasEquipos'])->name('bajas-equipos');
    Route::get('/bajas-equipos/imprimir', [$c, 'imprimirBajasEquipos'])->name('bajas-equipos.imprimir');

    Route::get('/bajas-empleados', [$c, 'bajasEmpleados'])->name('bajas-empleados');
    Route::get('/bajas-empleados/imprimir', [$c, 'imprimirBajasEmpleados'])->name('bajas-empleados.imprimir');

    Route::get('/traslados', [$c, 'traslados'])->name('traslados');
    Route::get('/traslados/imprimir', [$c, 'imprimirTraslados'])->name('traslados.imprimir');

    Route::get('/reemplazo', [$c, 'reemplazo'])->name('reemplazo');
    Route::get('/reemplazo/imprimir', [$c, 'imprimirReemplazo'])->name('reemplazo.imprimir');

    Route::get('/equipos-disponibles', [$c, 'disponibles'])->name('disponibles');
    Route::get('/equipos-disponibles/imprimir', [$c, 'imprimirDisponibles'])->name('disponibles.imprimir');

    Route::get('/mantenimientos', [$c, 'mantenimientos'])->name('mantenimientos');
    Route::get('/mantenimientos/imprimir', [$c, 'imprimirMantenimientos'])->name('mantenimientos.imprimir');
});

});
