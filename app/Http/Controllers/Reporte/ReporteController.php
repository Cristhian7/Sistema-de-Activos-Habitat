<?php

namespace App\Http\Controllers\Reporte;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use App\Models\Afiliado\Afiliado;
use App\Models\Baja\Baja;
use App\Models\Empleado\Empleado;
use App\Models\Equipo\Equipo;
use App\Models\Estado\Estado;
use App\Models\Mantenimiento\Mantenimiento;
use App\Models\Traslado\Traslado;

/**
 * Reportería.
 *
 * Ningún reporte tiene tabla propia: todos se calculan con los datos que ya existen.
 * Cada reporte se ve en pantalla (con filtros) y se imprime con los mismos filtros:
 * la ruta de impresión es la misma URL más "/imprimir" y recibe la misma query string.
 */
class ReporteController extends Controller
{
    // Regla de reposición: un equipo se puede reemplazar cuando cumple este tiempo desde su compra
    public const ANOS_REEMPLAZO = 3;

    public const MESES = [
        1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio',
        7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
    ];

    /**
     * Catálogo de reportes: lo usan el submenú lateral y la página de inicio de reportería.
     * Para agregar un reporte basta con sumarlo aquí y crear su ruta y su vista.
     */
    public static function catalogo(): array
    {
        return [
            ['ruta' => 'reportes.bajas-equipos', 'titulo' => 'Bajas de equipos', 'icono' => 'bi-trash',
                'descripcion' => 'Cantidad y listado de equipos dados de baja. Busca por código contable, ST, año, mes y afiliado.'],
            ['ruta' => 'reportes.bajas-empleados', 'titulo' => 'Bajas de empleados', 'icono' => 'bi-person-x',
                'descripcion' => 'Cantidad de empleados dados de baja por afiliado y por fecha.'],
            ['ruta' => 'reportes.traslados', 'titulo' => 'Traslados realizados', 'icono' => 'bi-arrow-left-right',
                'descripcion' => 'Cantidad de traslados por fecha, por mes y por técnico.'],
            ['ruta' => 'reportes.reemplazo', 'titulo' => 'Equipos por reemplazar', 'icono' => 'bi-arrow-repeat',
                'descripcion' => 'Equipos que cumplieron ' . self::ANOS_REEMPLAZO . ' años desde su compra y ya se pueden reemplazar.'],
            ['ruta' => 'reportes.disponibles', 'titulo' => 'Equipos disponibles', 'icono' => 'bi-box-seam',
                'descripcion' => 'Equipos activos que no están asignados a ningún empleado.'],
            ['ruta' => 'reportes.mantenimientos', 'titulo' => 'Mantenimientos realizados', 'icono' => 'bi-tools',
                'descripcion' => 'Cantidad de mantenimientos por fecha, por mes y por técnico.'],
        ];
    }

    public function index()
    {
        return view('Reporte.index', ['reportes' => self::catalogo()]);
    }

    // ------------------------------------------------------------------
    // 1. Bajas de equipos
    // ------------------------------------------------------------------
    public function bajasEquipos(Request $request, bool $imprimir = false)
    {
        $f = [
            'codigo'   => $this->texto($request->query('codigo')),
            'st'       => $this->texto($request->query('st')),
            'anio'     => $this->entero($request->query('anio'), 1900, 2200),
            'mes'      => $this->entero($request->query('mes'), 1, 12),
            'afiliado' => $this->entero($request->query('afiliado')),
        ];

        $bajas = Baja::with(['equipo.tipoEquipo', 'equipo.marca', 'equipo.modelo', 'empleado.afiliado', 'tecnico'])
            ->when($f['codigo'], fn ($q, $v) => $q->whereHas('equipo', fn ($e) => $e->where('codigo_contable', 'like', "%{$v}%")))
            ->when($f['st'], fn ($q, $v) => $q->whereHas('equipo', fn ($e) => $e->where('st', 'like', "%{$v}%")))
            ->when($f['anio'], fn ($q, $v) => $q->whereYear('fecha_baja', $v))
            ->when($f['mes'], fn ($q, $v) => $q->whereMonth('fecha_baja', $v))
            ->when($f['afiliado'], fn ($q, $v) => $q->whereHas('empleado', fn ($e) => $e->where('afiliado_id_afiliado', $v)))
            ->orderByDesc('fecha_baja')
            ->orderByDesc('id_baja')
            ->get();

        $porAfiliado = $bajas
            ->groupBy(fn ($b) => $b->empleado->afiliado->nombre_afiliado ?? 'Sin afiliado')
            ->map(fn ($g, $k) => ['etiqueta' => $k, 'cantidad' => $g->count()])
            ->sortByDesc('cantidad')
            ->values();

        return $this->vista('bajas-equipos', $imprimir, 'Bajas de equipos', [
            'bajas'       => $bajas,
            'totalGeneral' => Baja::count(),
            'conReemplazo' => $bajas->where('necesita_reemplazo', 'SI')->count(),
            'porAfiliado' => $porAfiliado,
            'anios'       => Baja::whereNotNull('fecha_baja')->selectRaw('YEAR(fecha_baja) as anio')->distinct()->orderByDesc('anio')->pluck('anio'),
            'afiliados'   => Afiliado::orderBy('nombre_afiliado')->get(),
            'meses'       => self::MESES,
            'f'           => $f,
        ], [
            'Código contable' => $f['codigo'],
            'ST'              => $f['st'],
            'Año de baja'     => $f['anio'],
            'Mes de baja'     => $f['mes'] ? self::MESES[$f['mes']] : null,
            'Afiliado'        => $this->nombreAfiliado($f['afiliado']),
        ]);
    }

    // ------------------------------------------------------------------
    // 2. Bajas de empleados
    // ------------------------------------------------------------------
    public function bajasEmpleados(Request $request, bool $imprimir = false)
    {
        $f = [
            'afiliado' => $this->entero($request->query('afiliado')),
            'desde'    => $this->fecha($request->query('desde')),
            'hasta'    => $this->fecha($request->query('hasta')),
        ];

        $baja = Estado::idBaja();

        $empleados = Empleado::with(['puesto', 'afiliado'])
            ->where('estado_id_estado', $baja)
            ->when($f['afiliado'], fn ($q, $v) => $q->where('afiliado_id_afiliado', $v))
            ->when($f['desde'], fn ($q, $v) => $q->whereDate('fecha_baja', '>=', $v))
            ->when($f['hasta'], fn ($q, $v) => $q->whereDate('fecha_baja', '<=', $v))
            ->orderByDesc('fecha_baja')
            ->orderBy('nombre_empleado')
            ->get();

        $porAfiliado = $empleados
            ->groupBy(fn ($e) => $e->afiliado->nombre_afiliado ?? 'Sin afiliado')
            ->map(fn ($g, $k) => ['etiqueta' => $k, 'cantidad' => $g->count()])
            ->sortByDesc('cantidad')
            ->values();

        return $this->vista('bajas-empleados', $imprimir, 'Bajas de empleados', [
            'empleados'    => $empleados,
            'totalGeneral' => Empleado::where('estado_id_estado', $baja)->count(),
            'sinFecha'     => $empleados->whereNull('fecha_baja')->count(),
            'porAfiliado'  => $porAfiliado,
            'porMes'       => $this->resumenPorMes($empleados, 'fecha_baja'),
            'afiliados'    => Afiliado::orderBy('nombre_afiliado')->get(),
            'f'            => $f,
        ], [
            'Afiliado' => $this->nombreAfiliado($f['afiliado']),
            'Desde'    => $this->mostrarFecha($f['desde']),
            'Hasta'    => $this->mostrarFecha($f['hasta']),
        ]);
    }

    // ------------------------------------------------------------------
    // 3. Traslados realizados
    // ------------------------------------------------------------------
    public function traslados(Request $request, bool $imprimir = false)
    {
        $f = $this->filtrosFechaYTecnico($request);

        $traslados = Traslado::with(['equipo', 'empleado.afiliado', 'tecnico'])
            ->when($f['desde'], fn ($q, $v) => $q->whereDate('fecha_envio', '>=', $v))
            ->when($f['hasta'], fn ($q, $v) => $q->whereDate('fecha_envio', '<=', $v))
            ->when($f['mes'], fn ($q, $v) => $q->whereRaw("DATE_FORMAT(fecha_envio, '%Y-%m') = ?", [$v]))
            ->when($f['tecnico'], fn ($q, $v) => $q->where('tecnico_id_empleado', $v))
            ->orderByDesc('fecha_envio')
            ->orderByDesc('id_traslado')
            ->get();

        return $this->vista('traslados', $imprimir, 'Traslados realizados', [
            'traslados'    => $traslados,
            'totalGeneral' => Traslado::count(),
            'porMes'       => $this->resumenPorMes($traslados, 'fecha_envio'),
            'porTecnico'   => $this->resumenPorTecnico($traslados),
            'tecnicos'     => $this->tecnicosDe(Traslado::class),
            'f'            => $f,
        ], $this->textoFiltrosFechaYTecnico($f));
    }

    // ------------------------------------------------------------------
    // 4. Equipos que ya se pueden reemplazar (3 años desde la compra)
    // ------------------------------------------------------------------
    public function reemplazo(Request $request, bool $imprimir = false)
    {
        $f = [
            'codigo'   => $this->texto($request->query('codigo')),
            'empleado' => $this->texto($request->query('empleado')),
            'afiliado' => $this->entero($request->query('afiliado')),
        ];

        $hoy = now();
        $limite = $hoy->copy()->subYears(self::ANOS_REEMPLAZO)->toDateString();

        $todos = Equipo::with(['tipoEquipo', 'marca', 'modelo', 'asignacionActual.empleado.afiliado'])
            ->where('estado_id_estado', '!=', Estado::idBaja())
            ->whereNotNull('fecha_compra')
            ->where('fecha_compra', '<=', $limite)
            ->when($f['codigo'], fn ($q, $v) => $q->where('codigo_contable', 'like', "%{$v}%"))
            ->orderBy('fecha_compra')
            ->orderBy('codigo_contable')
            ->get();

        $filas = $todos->map(function (Equipo $equipo) use ($hoy) {
            $empleado = $equipo->asignacionActual?->empleado;
            $compra = Carbon::parse($equipo->fecha_compra);

            return [
                'equipo'      => $equipo,
                'empleado'    => $empleado,
                'nombre'      => $empleado ? trim($empleado->nombre_empleado . ' ' . $empleado->apellido_empleado) : null,
                'afiliado'    => $empleado?->afiliado,
                'antiguedad'  => $this->antiguedad($compra, $hoy),
                'cumplio'     => $compra->copy()->addYears(self::ANOS_REEMPLAZO),
            ];
        });

        $filtradas = $filas
            ->when($f['empleado'], fn ($c, $v) => $c->filter(fn ($r) => $r['nombre'] && mb_stripos($r['nombre'], $v) !== false))
            ->when($f['afiliado'], fn ($c, $v) => $c->filter(fn ($r) => $r['afiliado'] && (int) $r['afiliado']->id_afiliado === $v))
            ->values();

        return $this->vista('reemplazo', $imprimir, 'Equipos por reemplazar', [
            'filas'        => $filtradas,
            'totalReemplazables' => $filas->count(),
            'asignados'    => $filtradas->whereNotNull('empleado')->count(),
            'disponibles'  => $filtradas->whereNull('empleado')->count(),
            'afiliados'    => Afiliado::orderBy('nombre_afiliado')->get(),
            'anos'         => self::ANOS_REEMPLAZO,
            'f'            => $f,
        ], [
            'Regla'            => 'Equipos con ' . self::ANOS_REEMPLAZO . ' años o más desde la compra (al ' . $hoy->format('d/m/Y') . ')',
            'Código contable'  => $f['codigo'],
            'Empleado'         => $f['empleado'],
            'Afiliado'         => $this->nombreAfiliado($f['afiliado']),
        ]);
    }

    // ------------------------------------------------------------------
    // 5. Equipos disponibles (sin asignar)
    // ------------------------------------------------------------------
    public function disponibles(Request $request, bool $imprimir = false)
    {
        $f = [
            'codigo' => $this->texto($request->query('codigo')),
            'st'     => $this->texto($request->query('st')),
        ];

        $base = Equipo::where('estado_id_estado', '!=', Estado::idBaja())->doesntHave('asignacionActual');

        $equipos = (clone $base)
            ->with(['tipoEquipo', 'marca', 'modelo'])
            ->when($f['codigo'], fn ($q, $v) => $q->where('codigo_contable', 'like', "%{$v}%"))
            ->when($f['st'], fn ($q, $v) => $q->where('st', 'like', "%{$v}%"))
            ->orderBy('codigo_contable')
            ->get();

        return $this->vista('disponibles', $imprimir, 'Equipos disponibles', [
            'equipos'       => $equipos,
            'totalActivos'  => Equipo::where('estado_id_estado', '!=', Estado::idBaja())->count(),
            'valorLibros'   => $equipos->sum('valor_libros'),
            'f'             => $f,
        ], [
            'Código contable' => $f['codigo'],
            'ST'              => $f['st'],
        ]);
    }

    // ------------------------------------------------------------------
    // 6. Mantenimientos realizados
    // ------------------------------------------------------------------
    public function mantenimientos(Request $request, bool $imprimir = false)
    {
        $f = $this->filtrosFechaYTecnico($request) + ['codigo' => $this->texto($request->query('codigo'))];

        $mantenimientos = Mantenimiento::with(['equipo', 'empleado.afiliado', 'tecnico'])
            ->when($f['codigo'], fn ($q, $v) => $q->whereHas('equipo', fn ($e) => $e->where('codigo_contable', 'like', "%{$v}%")))
            ->when($f['desde'], fn ($q, $v) => $q->whereDate('fecha_mantenimiento', '>=', $v))
            ->when($f['hasta'], fn ($q, $v) => $q->whereDate('fecha_mantenimiento', '<=', $v))
            ->when($f['mes'], fn ($q, $v) => $q->whereRaw("DATE_FORMAT(fecha_mantenimiento, '%Y-%m') = ?", [$v]))
            ->when($f['tecnico'], fn ($q, $v) => $q->where('tecnico_id_empleado', $v))
            ->orderByDesc('fecha_mantenimiento')
            ->orderByDesc('id_mantenimiento')
            ->get();

        return $this->vista('mantenimientos', $imprimir, 'Mantenimientos realizados', [
            'mantenimientos' => $mantenimientos,
            'totalGeneral'   => Mantenimiento::count(),
            'porMes'         => $this->resumenPorMes($mantenimientos, 'fecha_mantenimiento'),
            'porTecnico'     => $this->resumenPorTecnico($mantenimientos),
            'tecnicos'       => $this->tecnicosDe(Mantenimiento::class),
            'f'              => $f,
        ], ['Código contable' => $f['codigo']] + $this->textoFiltrosFechaYTecnico($f));
    }

    // ------------------------------------------------------------------
    // Impresión: misma pantalla pero con la hoja de impresión
    // ------------------------------------------------------------------
    public function imprimirBajasEquipos(Request $request)     { return $this->bajasEquipos($request, true); }
    public function imprimirBajasEmpleados(Request $request)   { return $this->bajasEmpleados($request, true); }
    public function imprimirTraslados(Request $request)        { return $this->traslados($request, true); }
    public function imprimirReemplazo(Request $request)        { return $this->reemplazo($request, true); }
    public function imprimirDisponibles(Request $request)      { return $this->disponibles($request, true); }
    public function imprimirMantenimientos(Request $request)   { return $this->mantenimientos($request, true); }

    // ------------------------------------------------------------------
    // Utilidades
    // ------------------------------------------------------------------

    /**
     * Arma la vista del reporte. $filtrosTexto es la lista "etiqueta => valor" de filtros aplicados
     * (los vacíos se descartan) y se muestra en la hoja impresa.
     */
    private function vista(string $nombre, bool $imprimir, string $titulo, array $datos, array $filtrosTexto)
    {
        $usuario = Auth::user();
        $usuario?->loadMissing('empleado');
        $emisor = $usuario?->empleado;

        return view('Reporte.' . $nombre, $datos + [
            'imprimir'   => $imprimir,
            'titulo'     => $titulo,
            'filtrosTexto' => array_filter($filtrosTexto, fn ($v) => $v !== null && $v !== ''),
            'generadoPor' => $emisor ? trim($emisor->nombre_empleado . ' ' . $emisor->apellido_empleado) : null,
        ]);
    }

    // Filtros comunes de los reportes por fecha, mes y técnico
    private function filtrosFechaYTecnico(Request $request): array
    {
        $mes = $request->query('mes');

        return [
            'desde'   => $this->fecha($request->query('desde')),
            'hasta'   => $this->fecha($request->query('hasta')),
            'mes'     => is_string($mes) && preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $mes) ? $mes : null,
            'tecnico' => $this->entero($request->query('tecnico')),
        ];
    }

    private function textoFiltrosFechaYTecnico(array $f): array
    {
        return [
            'Desde'   => $this->mostrarFecha($f['desde']),
            'Hasta'   => $this->mostrarFecha($f['hasta']),
            'Mes'     => $f['mes'] ? $this->etiquetaMes($f['mes']) : null,
            'Técnico' => $f['tecnico'] ? $this->nombreEmpleado($f['tecnico']) : null,
        ];
    }

    // Técnicos que aparecen en una tabla (traslado o mantenimiento), para el filtro
    private function tecnicosDe(string $modelo): Collection
    {
        $ids = $modelo::whereNotNull('tecnico_id_empleado')->distinct()->pluck('tecnico_id_empleado');

        return Empleado::whereIn('id_empleado', $ids)->orderBy('nombre_empleado')->get();
    }

    // Cantidad por mes (más reciente primero). Los registros sin fecha van aparte.
    private function resumenPorMes(Collection $items, string $campoFecha): Collection
    {
        return $items
            ->groupBy(fn ($i) => $i->$campoFecha ? Carbon::parse($i->$campoFecha)->format('Y-m') : 'sin-fecha')
            ->sortKeysDesc()
            ->map(fn ($grupo, $clave) => [
                'etiqueta' => $clave === 'sin-fecha' ? 'Sin fecha registrada' : $this->etiquetaMes($clave),
                'cantidad' => $grupo->count(),
            ])
            ->values();
    }

    // Cantidad por técnico (el que más hizo primero)
    private function resumenPorTecnico(Collection $items): Collection
    {
        return $items
            ->groupBy(fn ($i) => $i->tecnico_id_empleado ?? 0)
            ->map(function ($grupo) {
                $tecnico = $grupo->first()->tecnico;

                return [
                    'etiqueta' => $tecnico ? trim($tecnico->nombre_empleado . ' ' . $tecnico->apellido_empleado) : 'Sin técnico registrado',
                    'cantidad' => $grupo->count(),
                ];
            })
            ->sortByDesc('cantidad')
            ->values();
    }

    private function etiquetaMes(string $anioMes): string
    {
        [$anio, $mes] = explode('-', $anioMes);

        return self::MESES[(int) $mes] . ' ' . $anio;
    }

    private function antiguedad(Carbon $desde, Carbon $hasta): string
    {
        $diff = $desde->diff($hasta);
        $anos = $diff->y . ($diff->y === 1 ? ' año' : ' años');

        return $diff->m > 0 ? $anos . ' y ' . $diff->m . ($diff->m === 1 ? ' mes' : ' meses') : $anos;
    }

    private function nombreAfiliado(?int $id): ?string
    {
        return $id ? Afiliado::where('id_afiliado', $id)->value('nombre_afiliado') : null;
    }

    private function nombreEmpleado(int $id): ?string
    {
        $e = Empleado::find($id);

        return $e ? trim($e->nombre_empleado . ' ' . $e->apellido_empleado) : null;
    }

    private function mostrarFecha(?string $fecha): ?string
    {
        return $fecha ? Carbon::parse($fecha)->format('d/m/Y') : null;
    }

    // Valores de la query string: se limpian para que un valor raro nunca rompa la consulta
    private function texto($valor): ?string
    {
        if (!is_string($valor)) {
            return null;
        }

        $valor = trim($valor);

        return $valor === '' ? null : mb_substr($valor, 0, 100);
    }

    private function entero($valor, int $min = 1, int $max = PHP_INT_MAX): ?int
    {
        if (!is_string($valor) || !ctype_digit($valor) || strlen($valor) > 10) {
            return null;
        }

        $n = (int) $valor;

        return $n >= $min && $n <= $max ? $n : null;
    }

    private function fecha($valor): ?string
    {
        if (!is_string($valor) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $valor)) {
            return null;
        }

        try {
            return Carbon::createFromFormat('Y-m-d', $valor)->toDateString() === $valor ? $valor : null;
        } catch (\Throwable $e) {
            return null;
        }
    }
}
