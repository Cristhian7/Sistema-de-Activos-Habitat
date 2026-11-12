<?php

namespace App\Http\Controllers\Tarjeta;

use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use App\Models\Empleado\Empleado;
use App\Models\Estado\Estado;
use App\Models\Traslado\Traslado;

/**
 * Tarjeta de responsabilidad.
 *
 * No tiene tabla propia: los equipos que un empleado tiene bajo su responsabilidad
 * salen de sus traslados vigentes (equipo sin devolver y no dado de baja).
 */
class TarjetaController extends Controller
{
    private const RELACIONES_EQUIPO = ['equipo.tipoEquipo', 'equipo.marca', 'equipo.modelo', 'equipo.estado'];

    // Listado de empleados con los equipos que tienen a su cargo
    public function index()
    {
        $empleados = Empleado::with(['puesto', 'afiliado', 'estado'])
            ->orderBy('nombre_empleado')
            ->orderBy('apellido_empleado')
            ->get();

        // Un solo consulta para todos: traslados vigentes cuyo equipo no está dado de baja
        $abiertos = $this->trasladosAbiertos()->get()->groupBy('empleado_id_empleado');

        $filas = $empleados->map(function (Empleado $empleado) use ($abiertos) {
            return array_merge(
                ['empleado' => $empleado],
                $this->repartir($empleado, $abiertos->get($empleado->id_empleado, collect()))
            );
        });

        return view('tarjeta.index', compact('filas'));
    }

    // Tarjeta de responsabilidad de un empleado
    public function show($id)
    {
        $empleado = Empleado::with(['puesto', 'afiliado', 'estado'])->findOrFail($id);

        $datos = $this->tarjetaDe($empleado);

        return view('tarjeta.show', ['empleado' => $empleado] + $datos);
    }

    // Hoja de impresión de la tarjeta (firma el empleado y el técnico que la emite)
    public function imprimir($id)
    {
        $empleado = Empleado::with(['puesto', 'afiliado', 'estado'])->findOrFail($id);

        $datos = $this->tarjetaDe($empleado);

        // Técnico que emite la tarjeta = empleado del usuario logueado
        $usuario = Auth::user();
        $usuario?->loadMissing(['empleado.puesto', 'empleado.afiliado']);
        $tecnico = $usuario?->empleado;

        return view('tarjeta.print', ['empleado' => $empleado, 'tecnico' => $tecnico] + $datos);
    }

    // Traslados vigentes (equipo sin devolver) cuyo equipo no está dado de baja
    private function trasladosAbiertos()
    {
        return Traslado::vigentes()
            ->whereHas('equipo', fn ($q) => $q->where('estado_id_estado', '!=', Estado::idBaja()))
            ->with(self::RELACIONES_EQUIPO)
            ->orderBy('fecha_envio')
            ->orderBy('id_traslado');
    }

    private function tarjetaDe(Empleado $empleado): array
    {
        $abiertos = $this->trasladosAbiertos()
            ->where('empleado_id_empleado', $empleado->id_empleado)
            ->get();

        return $this->repartir($empleado, $abiertos);
    }

    /**
     * Separa los traslados abiertos de un empleado:
     *  - aCargo      : equipos que realmente tiene bajo su responsabilidad
     *  - sinDevolver : equipos que figuran abiertos pero el empleado ya fue dado de baja
     *                  (hay que regularizarlos devolviéndolos en Traslados)
     */
    private function repartir(Empleado $empleado, Collection $abiertos): array
    {
        if (Estado::esBaja($empleado->estado_id_estado)) {
            return ['aCargo' => collect(), 'sinDevolver' => $abiertos->values()];
        }

        return ['aCargo' => $abiertos->values(), 'sinDevolver' => collect()];
    }
}
