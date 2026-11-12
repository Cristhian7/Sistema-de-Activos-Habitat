<?php

namespace App\Http\Controllers\Notificacion;

use App\Http\Controllers\Controller;
use App\Models\Notificacion\Notificacion;
use App\Support\Notificaciones;

/**
 * Cada usuario ve y marca como revisadas solo SUS notificaciones.
 */
class NotificacionController extends Controller
{
    private const REVISADAS_A_MOSTRAR = 30;

    public function index()
    {
        $empleado = Notificaciones::empleadoActual();

        $pendientes = $empleado
            ? Notificacion::de($empleado)->pendientes()->orderByDesc('creado_en')->orderByDesc('id_notificacion')->get()
            : collect();

        $revisadas = $empleado
            ? Notificacion::de($empleado)->whereNotNull('revisado_en')->orderByDesc('revisado_en')->limit(self::REVISADAS_A_MOSTRAR)->get()
            : collect();

        return view('Notificacion.index', compact('pendientes', 'revisadas'));
    }

    // Check de "revisado" de una notificación
    public function revisar($id)
    {
        $notificacion = $this->propia($id);

        if (!$notificacion->revisada) {
            $notificacion->update(['revisado_en' => now()]);
        }

        return back()->with('success', 'Marcado como revisado.');
    }

    public function revisarTodas()
    {
        $empleado = Notificaciones::empleadoActual();

        $total = $empleado
            ? Notificacion::de($empleado)->pendientes()->update(['revisado_en' => now()])
            : 0;

        return back()->with('success', $total > 0 ? 'Se marcaron como revisadas ' . $total . ' notificaciones.' : 'No había notificaciones pendientes.');
    }

    // La notificación debe ser del usuario actual; si no, 404 (no se revela que existe)
    private function propia($id): Notificacion
    {
        $empleado = Notificaciones::empleadoActual();

        return Notificacion::de($empleado ?? 0)->where('id_notificacion', $id)->firstOrFail();
    }
}
