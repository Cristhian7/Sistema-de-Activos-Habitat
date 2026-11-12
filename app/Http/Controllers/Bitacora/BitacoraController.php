<?php

namespace App\Http\Controllers\Bitacora;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use App\Models\Baja\Baja;
use App\Models\Equipo\Equipo;
use App\Models\Estado\Estado;
use App\Models\Traslado\Traslado;

/**
 * Bitácora de equipos.
 *
 * No tiene tabla propia: el historial de cada equipo se arma leyendo
 *  - los traslados      (a quién se asignó el equipo),
 *  - las devoluciones   (cuándo se le quitó al empleado, guardadas en el propio traslado) y
 *  - las bajas          (cuándo dejó de usarse).
 */
class BitacoraController extends Controller
{
    // Fase de cada movimiento dentro de un mismo traslado (para ordenar el mismo día)
    private const FASE_TRASLADO = 0;
    private const FASE_DEVOLUCION = 1;
    private const FASE_BAJA = 2;

    // Las bajas cierran el historial del equipo: van después de cualquier traslado
    private const ID_BAJA_AL_FINAL = 9999999999;

    // Listado de equipos con su situación actual
    public function index()
    {
        $equipos = Equipo::with(['tipoEquipo', 'marca', 'modelo', 'estado'])
            ->orderBy('codigo_contable')
            ->get();

        $traslados = Traslado::with($this->relacionesTraslado())->get()->groupBy('equipo_id_equipo');
        $bajas = Baja::with($this->relacionesBaja())->get()->groupBy('equipo_id_equipo');

        $filas = $equipos->map(function (Equipo $equipo) use ($traslados, $bajas) {
            $movimientos = $this->movimientos(
                $traslados->get($equipo->id_equipo, collect()),
                $bajas->get($equipo->id_equipo, collect())
            );

            return [
                'equipo'      => $equipo,
                'movimientos' => $movimientos,
                'situacion'   => $this->situacion($equipo, $movimientos),
            ];
        });

        return view('bitacora.index', compact('filas'));
    }

    // Historial completo de un equipo
    public function show($id)
    {
        $equipo = Equipo::with(['tipoEquipo', 'marca', 'modelo', 'estado'])->findOrFail($id);

        $traslados = Traslado::with($this->relacionesTraslado())->where('equipo_id_equipo', $equipo->id_equipo)->get();
        $bajas = Baja::with($this->relacionesBaja())->where('equipo_id_equipo', $equipo->id_equipo)->get();

        $movimientos = $this->movimientos($traslados, $bajas);
        $situacion = $this->situacion($equipo, $movimientos);

        return view('bitacora.show', compact('equipo', 'movimientos', 'situacion'));
    }

    private function relacionesTraslado(): array
    {
        return ['empleado.puesto', 'empleado.afiliado', 'tecnico', 'tecnicoDevolucion'];
    }

    private function relacionesBaja(): array
    {
        return ['empleado.puesto', 'empleado.afiliado', 'tecnico'];
    }

    /**
     * Une traslados, devoluciones y bajas en una sola lista, del más reciente al más antiguo.
     * Cada movimiento es un arreglo con: tipo, fecha, empleado, tecnico, titulo, detalle, url.
     */
    private function movimientos(Collection $traslados, Collection $bajas): Collection
    {
        $lista = collect();

        foreach ($traslados as $t) {
            $lista->push([
                'tipo'     => 'traslado',
                'id'       => $t->id_traslado,
                'fecha'    => $t->fecha_envio ? Carbon::parse($t->fecha_envio) : null,
                'vigente'  => $t->vigente,
                'clave'    => $this->clave($t->fecha_envio, $t->id_traslado, self::FASE_TRASLADO),
                'empleado' => $t->empleado,
                'tecnico'  => $t->tecnico,
                'titulo'   => 'Traslado',
                'badge'    => 'bg-primary',
                'icono'    => 'bi-arrow-left-right',
                'rol'      => 'Recibió el equipo',
                'detalle'  => $this->unir([$t->motivo_traslado, $t->descripcion]),
                'url'      => route('traslados.show', $t->id_traslado),
            ]);

            // Si el equipo se le quitó al empleado, la devolución es un movimiento aparte.
            // Su título dice la causa: baja del empleado, reasignación o devolución normal.
            if (!$t->vigente) {
                [$titulo, $badge, $icono] = match ($t->causa_devolucion) {
                    Traslado::CAUSA_BAJA_EMPLEADO => ['Baja del empleado', 'bg-dark', 'bi-person-x'],
                    Traslado::CAUSA_REASIGNACION  => ['Reasignación', 'bg-info text-dark', 'bi-arrow-repeat'],
                    default                       => ['Devolución', 'bg-warning text-dark', 'bi-box-arrow-in-left'],
                };

                $lista->push([
                    'tipo'     => 'devolucion',
                    'id'       => $t->id_traslado,
                    'fecha'    => Carbon::parse($t->fecha_devolucion),
                    'vigente'  => false,
                    'clave'    => $this->clave($t->fecha_devolucion, $t->id_traslado, self::FASE_DEVOLUCION),
                    'empleado' => $t->empleado,
                    'tecnico'  => $t->tecnicoDevolucion,
                    'titulo'   => $titulo,
                    'badge'    => $badge,
                    'icono'    => $icono,
                    'rol'      => $t->causa_devolucion === Traslado::CAUSA_BAJA_EMPLEADO ? 'Empleado dado de baja' : 'Devolvió el equipo',
                    'detalle'  => $this->unir([$t->motivo_devolucion]),
                    'url'      => route('traslados.show', $t->id_traslado),
                ]);
            }
        }

        foreach ($bajas as $b) {
            $lista->push([
                'tipo'     => 'baja',
                'id'       => $b->id_baja,
                'fecha'    => $b->fecha_baja ? Carbon::parse($b->fecha_baja) : null,
                'vigente'  => false,
                'clave'    => $this->clave($b->fecha_baja, self::ID_BAJA_AL_FINAL, self::FASE_BAJA),
                'empleado' => $b->empleado,
                'tecnico'  => $b->tecnico,
                'titulo'   => 'Baja del equipo',
                'badge'    => 'bg-danger',
                'icono'    => 'bi-trash',
                'rol'      => 'Último usuario',
                'detalle'  => $this->unir([$b->problema, $b->detalle]),
                'url'      => route('bajas.show', $b->id_baja),
            ]);
        }

        // Más reciente primero
        return $lista->sortByDesc('clave')->values();
    }

    /**
     * Clave de orden: fecha, luego el traslado al que pertenece el movimiento y por último su fase.
     * Así el mismo día queda: traslado A -> devolución de A -> traslado B (reasignación) -> baja.
     */
    private function clave($fecha, int $idTraslado, int $fase): string
    {
        $fecha = $fecha ? Carbon::parse($fecha)->format('Y-m-d') : '0000-00-00';

        return sprintf('%s-%010d-%d', $fecha, $idTraslado, $fase);
    }

    /**
     * Quién tiene el equipo hoy o quién fue el último en usarlo.
     *  - asignado   : traslado vigente (sin devolución) y el equipo no está de baja -> responsable actual
     *  - disponible : sin traslado vigente y el equipo no está de baja               -> libre para asignar
     *  - baja       : el equipo está dado de baja                                    -> último usuario
     */
    private function situacion(Equipo $equipo, Collection $movimientos): array
    {
        $ultimoTraslado = $movimientos->firstWhere('tipo', 'traslado');
        $ultimaDevolucion = $movimientos->firstWhere('tipo', 'devolucion');
        $ultimaBaja = $movimientos->firstWhere('tipo', 'baja');

        if (Estado::esBaja($equipo->estado_id_estado)) {
            // Último usuario: quien tenía el equipo según su último traslado; si no hay, el de la baja
            $origen = $ultimoTraslado ?? $ultimaBaja;

            return [
                'tipo'     => 'baja',
                'titulo'   => 'Último usuario',
                'estado'   => 'Dado de baja' . ($ultimaBaja ? '' : ' (sin registro de baja)'),
                'empleado' => $origen['empleado'] ?? null,
                'desde'    => $ultimoTraslado['fecha'] ?? null,
                'hasta'    => $ultimaBaja['fecha'] ?? null,
            ];
        }

        // Traslado abierto, pero el empleado ya fue dado de baja: el equipo quedó sin dueño
        if ($ultimoTraslado && $ultimoTraslado['vigente'] && Estado::esBaja($ultimoTraslado['empleado']->estado_id_estado ?? null)) {
            return [
                'tipo'     => 'disponible',
                'titulo'   => 'Último usuario',
                'estado'   => 'Disponible (el empleado fue dado de baja)',
                'empleado' => $ultimoTraslado['empleado'],
                'desde'    => $ultimoTraslado['fecha'],
                'hasta'    => null,
            ];
        }

        if ($ultimoTraslado && $ultimoTraslado['vigente']) {
            return [
                'tipo'     => 'asignado',
                'titulo'   => 'Responsable actual',
                'estado'   => 'Asignado',
                'empleado' => $ultimoTraslado['empleado'],
                'desde'    => $ultimoTraslado['fecha'],
                'hasta'    => null,
            ];
        }

        if ($ultimoTraslado) {
            // El último empleado ya devolvió el equipo: está libre para asignar
            return [
                'tipo'     => 'disponible',
                'titulo'   => 'Último usuario',
                'estado'   => 'Disponible',
                'empleado' => $ultimoTraslado['empleado'],
                'desde'    => $ultimoTraslado['fecha'],
                'hasta'    => $ultimaDevolucion['fecha'] ?? null,
            ];
        }

        return [
            'tipo'     => 'sin_asignar',
            'titulo'   => 'Situación actual',
            'estado'   => 'Disponible (nunca asignado)',
            'empleado' => null,
            'desde'    => null,
            'hasta'    => null,
        ];
    }

    // Une textos no vacíos con un separador
    private function unir(array $partes): string
    {
        $partes = array_filter(array_map(fn ($p) => trim((string) $p), $partes), fn ($p) => $p !== '');

        return implode(' — ', $partes);
    }
}
