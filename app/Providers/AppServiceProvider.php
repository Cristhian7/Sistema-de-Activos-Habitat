<?php

namespace App\Providers;

use App\Models\Notificacion\Notificacion;
use App\Support\Notificaciones;
use App\Support\Permisos;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // En las vistas: @puede('equipos.editar') ... @endpuede
        // Con varios permisos basta uno: @puede('traslados.crear', 'bajas.crear')
        Blade::if('puede', fn (string ...$permisos) => Permisos::puedeAlguno($permisos));

        // Campana de notificaciones del menú superior (solo para los roles que las reciben)
        View::composer('layouts.app', function ($vista) {
            if (!Auth::check() || !Notificaciones::recibe()) {
                return;
            }

            $empleado = Notificaciones::empleadoActual() ?? 0;
            $pendientes = Notificacion::de($empleado)->pendientes();

            $vista->with([
                'notifTotal'      => (clone $pendientes)->count(),
                'notifPendientes' => (clone $pendientes)->orderByDesc('creado_en')->orderByDesc('id_notificacion')->limit(5)->get(),
            ]);
        });
    }
}
