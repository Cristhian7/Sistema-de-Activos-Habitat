<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Reporte\ReporteController;
use App\Models\Baja\Baja;
use App\Models\Empleado\Empleado;
use App\Models\Equipo\Equipo;
use App\Models\Estado\Estado;
use App\Models\Mantenimiento\Mantenimiento;
use App\Models\Traslado\Traslado;
use App\Support\Permisos;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Panel general. Todo se calcula con los datos existentes, sin tablas propias.
 */
class DashboardController extends Controller
{
    private const MESES_GRAFICA = 6;
    private const ACTIVIDAD_RECIENTE = 8;

    public function index()
    {
        // Si el rol no puede ver el dashboard, se envía a su primera pantalla permitida
        if (!Permisos::puede('dashboard.ver')) {
            $inicio = Permisos::rutaInicio();

            abort_if($inicio === null, 403, 'Tu rol no tiene permiso para esta acción.');

            return redirect()->route($inicio);
        }

        $idBaja = Estado::idBaja();
        $hoy = now();

        // ---- Equipos -------------------------------------------------------
        $activos = Equipo::where('estado_id_estado', '!=', $idBaja);
        $totalActivos = (clone $activos)->count();

        // Asignado = traslado vigente de un empleado que no está de baja, sobre un equipo que no está de baja
        $asignados = Traslado::vigentes()
            ->deEmpleadoActivo()
            ->whereHas('equipo', fn ($q) => $q->where('estado_id_estado', '!=', $idBaja))
            ->distinct()
            ->count('equipo_id_equipo');

        $limiteReemplazo = $hoy->copy()->subYears(ReporteController::ANOS_REEMPLAZO)->toDateString();
        $porReemplazar = (clone $activos)->whereNotNull('fecha_compra')->where('fecha_compra', '<=', $limiteReemplazo)->count();

        // Traslados abiertos de empleados dados de baja: hay que regularizarlos (devolver el equipo)
        $sinDevolver = Traslado::vigentes()
            ->whereNotIn('id_traslado', Traslado::vigentes()->deEmpleadoActivo()->select('id_traslado'))
            ->whereHas('equipo', fn ($q) => $q->where('estado_id_estado', '!=', $idBaja))
            ->count();

        // ---- Resúmenes -----------------------------------------------------
        $porTipo = (clone $activos)->with('tipoEquipo')->get()
            ->groupBy(fn ($e) => $e->tipoEquipo->nombre_tipo ?? 'Sin tipo')
            ->map(fn ($g, $k) => ['etiqueta' => $k, 'cantidad' => $g->count()])
            ->sortByDesc('cantidad')->values();

        $porAfiliado = Traslado::vigentes()
            ->deEmpleadoActivo()
            ->whereHas('equipo', fn ($q) => $q->where('estado_id_estado', '!=', $idBaja))
            ->with('empleado.afiliado')->get()
            ->groupBy(fn ($t) => $t->empleado->afiliado->nombre_afiliado ?? 'Sin afiliado')
            ->map(fn ($g, $k) => ['etiqueta' => $k, 'cantidad' => $g->count()])
            ->sortByDesc('cantidad')->values();

        // ---- Mes actual ----------------------------------------------------
        $desde = $hoy->copy()->startOfMonth()->toDateString();
        $hasta = $hoy->copy()->endOfMonth()->toDateString();

        $delMes = [
            'traslados'      => Traslado::whereBetween('fecha_envio', [$desde, $hasta])->count(),
            'mantenimientos' => Mantenimiento::whereBetween('fecha_mantenimiento', [$desde, $hasta])->count(),
            'bajas'          => Baja::whereBetween('fecha_baja', [$desde, $hasta])->count(),
        ];

        // Traslados y bajas sin el documento firmado (resguardo pendiente de subir)
        $trasladosSinDocumento = Traslado::sinDocumento()->count();
        $bajasSinDocumento = Baja::sinDocumento()->count();

        return view('dashboard', [
            'trasladosSinDocumento' => $trasladosSinDocumento,
            'bajasSinDocumento' => $bajasSinDocumento,
            'totalActivos'   => $totalActivos,
            'asignados'      => $asignados,
            'disponibles'    => max($totalActivos - $asignados, 0),
            'equiposBaja'    => Equipo::where('estado_id_estado', $idBaja)->count(),
            'empleadosActivos' => Empleado::where('estado_id_estado', Estado::idActivo())->count(),
            'empleadosBaja'  => Empleado::where('estado_id_estado', $idBaja)->count(),
            'porReemplazar'  => $porReemplazar,
            'anosReemplazo'  => ReporteController::ANOS_REEMPLAZO,
            'sinDevolver'    => $sinDevolver,
            'delMes'         => $delMes,
            'nombreMes'      => ReporteController::MESES[(int) $hoy->format('n')] . ' ' . $hoy->format('Y'),
            'porTipo'        => $porTipo,
            'porAfiliado'    => $porAfiliado,
            'grafica'        => $this->ultimosMeses($hoy),
            'actividad'      => $this->actividadReciente(),
        ]);
    }

    /**
     * Traslados, mantenimientos y bajas de los últimos meses (incluye el actual).
     * Devuelve una fila por mes: ['etiqueta', 'traslados', 'mantenimientos', 'bajas'].
     */
    private function ultimosMeses(Carbon $hoy): Collection
    {
        $inicio = $hoy->copy()->startOfMonth()->subMonths(self::MESES_GRAFICA - 1);

        $contar = function (string $modelo, string $campo) use ($inicio) {
            return $modelo::where($campo, '>=', $inicio->toDateString())
                ->pluck($campo)
                ->countBy(fn ($f) => Carbon::parse($f)->format('Y-m'));
        };

        $traslados = $contar(Traslado::class, 'fecha_envio');
        $mantenimientos = $contar(Mantenimiento::class, 'fecha_mantenimiento');
        $bajas = $contar(Baja::class, 'fecha_baja');

        return collect(range(0, self::MESES_GRAFICA - 1))->map(function ($i) use ($inicio, $traslados, $mantenimientos, $bajas) {
            $mes = $inicio->copy()->addMonths($i);
            $clave = $mes->format('Y-m');

            return [
                'etiqueta'       => mb_substr(ReporteController::MESES[(int) $mes->format('n')], 0, 3) . ' ' . $mes->format('y'),
                'traslados'      => $traslados->get($clave, 0),
                'mantenimientos' => $mantenimientos->get($clave, 0),
                'bajas'          => $bajas->get($clave, 0),
            ];
        });
    }

    // Últimos movimientos del sistema: traslados, mantenimientos y bajas mezclados por fecha
    private function actividadReciente(): Collection
    {
        $n = self::ACTIVIDAD_RECIENTE;
        $lista = collect();

        foreach (Traslado::with(['equipo', 'empleado'])->orderByDesc('fecha_envio')->orderByDesc('id_traslado')->limit($n)->get() as $t) {
            $lista->push([
                'fecha' => $t->fecha_envio, 'orden' => $t->id_traslado, 'tipo' => 'Traslado',
                'badge' => 'bg-primary', 'icono' => 'bi-arrow-left-right',
                'equipo' => $t->equipo->codigo_contable ?? 'N/A',
                'detalle' => 'Para ' . $this->nombre($t->empleado),
                'url' => route('traslados.show', $t->id_traslado), 'permiso' => 'traslados.ver',
            ]);
        }

        foreach (Mantenimiento::with(['equipo', 'empleado'])->orderByDesc('fecha_mantenimiento')->orderByDesc('id_mantenimiento')->limit($n)->get() as $m) {
            $lista->push([
                'fecha' => $m->fecha_mantenimiento, 'orden' => $m->id_mantenimiento, 'tipo' => 'Mantenimiento',
                'badge' => 'bg-warning text-dark', 'icono' => 'bi-tools',
                'equipo' => $m->equipo->codigo_contable ?? 'N/A',
                'detalle' => \Illuminate\Support\Str::limit((string) $m->descripcion, 50),
                'url' => route('mantenimientos.show', $m->id_mantenimiento), 'permiso' => 'mantenimientos.ver',
            ]);
        }

        foreach (Baja::with(['equipo', 'empleado'])->orderByDesc('fecha_baja')->orderByDesc('id_baja')->limit($n)->get() as $b) {
            $lista->push([
                'fecha' => $b->fecha_baja, 'orden' => $b->id_baja, 'tipo' => 'Baja de equipo',
                'badge' => 'bg-danger', 'icono' => 'bi-trash',
                'equipo' => $b->equipo->codigo_contable ?? 'N/A',
                'detalle' => \Illuminate\Support\Str::limit((string) $b->problema, 50),
                'url' => route('bajas.show', $b->id_baja), 'permiso' => 'bajas.ver',
            ]);
        }

        return $lista
            ->sortByDesc(fn ($i) => sprintf('%s-%010d', $i['fecha'] ? Carbon::parse($i['fecha'])->format('Y-m-d') : '0000-00-00', $i['orden']))
            ->take($n)
            ->values();
    }

    private function nombre($empleado): string
    {
        return $empleado ? trim($empleado->nombre_empleado . ' ' . $empleado->apellido_empleado) : 'N/A';
    }
}
