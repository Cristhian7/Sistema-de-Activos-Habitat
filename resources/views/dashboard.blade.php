@extends('layouts.app')

@section('title', 'Dashboard - Sistema de Activos')

@section('content')
@php
    $maxGrafica = max(1, $grafica->max(fn ($m) => max($m['traslados'], $m['mantenimientos'], $m['bajas'])));
    $altoMax = 140; // alto en px de la barra más grande
    $altura = fn ($n) => $n > 0 ? max(4, (int) round($n / $maxGrafica * $altoMax)) : 2;
    $maxResumen = fn ($filas) => max(1, $filas->max('cantidad') ?? 1);
@endphp

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2 fw-bold text-dark">Panel General</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <span class="text-muted small">Resumen al {{ now()->format('d/m/Y') }}</span>
    </div>
</div>

<!-- Avisos que requieren atención -->
@if($sinDevolver > 0)
    @puede('tarjetas.ver')
    <div class="alert alert-warning d-flex align-items-center justify-content-between py-2">
        <div>
            <i class="bi bi-exclamation-triangle me-2"></i>
            <strong>{{ $sinDevolver }}</strong> {{ $sinDevolver === 1 ? 'equipo sigue asignado' : 'equipos siguen asignados' }} a empleados dados de baja. Hay que regularizarlos devolviéndolos en Traslados.
        </div>
        <a href="{{ route('tarjetas.index') }}" class="btn btn-sm btn-outline-dark">Ver tarjetas</a>
    </div>
    @endpuede
@endif
@if($trasladosSinDocumento > 0)
    @puede('traslados.ver')
    <div class="alert alert-warning d-flex align-items-center justify-content-between py-2">
        <div>
            <i class="bi bi-file-earmark-x me-2"></i>
            Falta subir el documento firmado de <strong>{{ $trasladosSinDocumento }}</strong> {{ $trasladosSinDocumento === 1 ? 'traslado' : 'traslados' }}.
        </div>
        <a href="{{ route('traslados.index') }}" class="btn btn-sm btn-outline-dark">Ver traslados</a>
    </div>
    @endpuede
@endif
@if($bajasSinDocumento > 0)
    @puede('bajas.ver')
    <div class="alert alert-warning d-flex align-items-center justify-content-between py-2">
        <div>
            <i class="bi bi-file-earmark-x me-2"></i>
            Falta subir el documento firmado de <strong>{{ $bajasSinDocumento }}</strong> {{ $bajasSinDocumento === 1 ? 'baja' : 'bajas' }}.
        </div>
        <a href="{{ route('bajas.index') }}" class="btn btn-sm btn-outline-dark">Ver bajas</a>
    </div>
    @endpuede
@endif
@if($porReemplazar > 0)
    <div class="alert alert-info d-flex align-items-center justify-content-between py-2">
        <div>
            <i class="bi bi-arrow-repeat me-2"></i>
            <strong>{{ $porReemplazar }}</strong> {{ $porReemplazar === 1 ? 'equipo cumplió' : 'equipos cumplieron' }} {{ $anosReemplazo }} años desde su compra y ya se {{ $porReemplazar === 1 ? 'puede' : 'pueden' }} reemplazar.
        </div>
        @puede('reportes.ver')
            <a href="{{ route('reportes.reemplazo') }}" class="btn btn-sm btn-outline-dark">Ver reporte</a>
        @endpuede
    </div>
@endif

<!-- Tarjetas de Estadísticas / Resumen -->
<div class="row g-3 mb-3">
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm p-3 h-100">
            <div class="d-flex align-items-center">
                <div class="bg-primary text-white p-3 rounded-3 me-3"><i class="bi bi-laptop fs-4"></i></div>
                <div>
                    <span class="text-muted small d-block">Equipos activos</span>
                    <h3 class="fw-bold mb-0">{{ $totalActivos }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm p-3 h-100">
            <div class="d-flex align-items-center">
                <div class="bg-warning text-dark p-3 rounded-3 me-3"><i class="bi bi-person-check fs-4"></i></div>
                <div>
                    <span class="text-muted small d-block">Equipos asignados</span>
                    <h3 class="fw-bold mb-0">{{ $asignados }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm p-3 h-100">
            <div class="d-flex align-items-center">
                <div class="bg-success text-white p-3 rounded-3 me-3"><i class="bi bi-box-seam fs-4"></i></div>
                <div>
                    <span class="text-muted small d-block">Equipos disponibles</span>
                    <h3 class="fw-bold mb-0">{{ $disponibles }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm p-3 h-100">
            <div class="d-flex align-items-center">
                <div class="bg-danger text-white p-3 rounded-3 me-3"><i class="bi bi-trash fs-4"></i></div>
                <div>
                    <span class="text-muted small d-block">Equipos dados de baja</span>
                    <h3 class="fw-bold mb-0">{{ $equiposBaja }}</h3>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm p-3 h-100">
            <div class="d-flex align-items-center">
                <div class="bg-info text-white p-3 rounded-3 me-3"><i class="bi bi-people fs-4"></i></div>
                <div>
                    <span class="text-muted small d-block">Empleados activos</span>
                    <h3 class="fw-bold mb-0">{{ $empleadosActivos }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm p-3 h-100">
            <div class="d-flex align-items-center">
                <div class="bg-secondary text-white p-3 rounded-3 me-3"><i class="bi bi-person-x fs-4"></i></div>
                <div>
                    <span class="text-muted small d-block">Empleados dados de baja</span>
                    <h3 class="fw-bold mb-0">{{ $empleadosBaja }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm p-3 h-100">
            <div class="d-flex align-items-center">
                <div class="bg-dark text-white p-3 rounded-3 me-3"><i class="bi bi-arrow-repeat fs-4"></i></div>
                <div>
                    <span class="text-muted small d-block">Por reemplazar ({{ $anosReemplazo }}+ años)</span>
                    <h3 class="fw-bold mb-0">{{ $porReemplazar }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm p-3 h-100">
            <div class="small text-muted mb-1">Movimiento de {{ $nombreMes }}</div>
            <div class="d-flex justify-content-between text-center">
                <div><div class="fw-bold fs-5">{{ $delMes['traslados'] }}</div><div class="small text-muted">Traslados</div></div>
                <div><div class="fw-bold fs-5">{{ $delMes['mantenimientos'] }}</div><div class="small text-muted">Mantenim.</div></div>
                <div><div class="fw-bold fs-5">{{ $delMes['bajas'] }}</div><div class="small text-muted">Bajas</div></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <!-- Gráfica de los últimos meses -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3">Movimientos de los últimos {{ $grafica->count() }} meses</h5>
                <div class="d-flex justify-content-around align-items-end" style="height: {{ $altoMax + 24 }}px;" role="img"
                     aria-label="Gráfica de traslados, mantenimientos y bajas por mes">
                    @foreach($grafica as $mes)
                        <div class="text-center flex-fill">
                            <div class="d-flex justify-content-center align-items-end gap-1" style="height: {{ $altoMax }}px;">
                                <div class="bg-primary rounded-top" style="width: 14px; height: {{ $altura($mes['traslados']) }}px;" title="Traslados: {{ $mes['traslados'] }}"></div>
                                <div class="bg-warning rounded-top" style="width: 14px; height: {{ $altura($mes['mantenimientos']) }}px;" title="Mantenimientos: {{ $mes['mantenimientos'] }}"></div>
                                <div class="bg-danger rounded-top" style="width: 14px; height: {{ $altura($mes['bajas']) }}px;" title="Bajas: {{ $mes['bajas'] }}"></div>
                            </div>
                            <div class="small text-muted mt-1">{{ $mes['etiqueta'] }}</div>
                        </div>
                    @endforeach
                </div>
                <div class="d-flex justify-content-center gap-3 small mt-3">
                    <span><span class="badge bg-primary">&nbsp;</span> Traslados</span>
                    <span><span class="badge bg-warning">&nbsp;</span> Mantenimientos</span>
                    <span><span class="badge bg-danger">&nbsp;</span> Bajas de equipos</span>
                </div>
                <div class="table-responsive mt-3">
                    <table class="table table-sm text-center small mb-0">
                        <thead class="table-light">
                            <tr><th class="text-start">Mes</th><th>Traslados</th><th>Mantenimientos</th><th>Bajas</th></tr>
                        </thead>
                        <tbody>
                            @foreach($grafica->reverse() as $mes)
                                <tr>
                                    <td class="text-start">{{ $mes['etiqueta'] }}</td>
                                    <td>{{ $mes['traslados'] }}</td>
                                    <td>{{ $mes['mantenimientos'] }}</td>
                                    <td>{{ $mes['bajas'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Distribución de equipos -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3">Equipos activos por tipo</h5>
                @php $max = $maxResumen($porTipo); @endphp
                @forelse($porTipo as $fila)
                    <div class="mb-2">
                        <div class="d-flex justify-content-between small"><span>{{ $fila['etiqueta'] }}</span><strong>{{ $fila['cantidad'] }}</strong></div>
                        <div class="progress" style="height: 8px;"><div class="progress-bar" style="width: {{ round($fila['cantidad'] / $max * 100) }}%"></div></div>
                    </div>
                @empty
                    <p class="text-muted small mb-0">No hay equipos activos.</p>
                @endforelse
            </div>
        </div>
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3">Equipos asignados por afiliado</h5>
                @php $max = $maxResumen($porAfiliado); @endphp
                @forelse($porAfiliado as $fila)
                    <div class="mb-2">
                        <div class="d-flex justify-content-between small"><span>{{ $fila['etiqueta'] }}</span><strong>{{ $fila['cantidad'] }}</strong></div>
                        <div class="progress" style="height: 8px;"><div class="progress-bar bg-success" style="width: {{ round($fila['cantidad'] / $max * 100) }}%"></div></div>
                    </div>
                @empty
                    <p class="text-muted small mb-0">No hay equipos asignados.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Actividad reciente -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <h5 class="fw-bold mb-3">Actividad reciente</h5>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Fecha</th>
                        <th>Movimiento</th>
                        <th>Código contable</th>
                        <th>Detalle</th>
                        <th class="text-end">Ver</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($actividad as $mov)
                        <tr>
                            <td>{{ $mov['fecha'] ? \Carbon\Carbon::parse($mov['fecha'])->format('d/m/Y') : 'N/A' }}</td>
                            <td><span class="badge {{ $mov['badge'] }}"><i class="bi {{ $mov['icono'] }} me-1"></i>{{ $mov['tipo'] }}</span></td>
                            <td class="fw-semibold">{{ $mov['equipo'] }}</td>
                            <td class="text-muted">{{ $mov['detalle'] }}</td>
                            <td class="text-end">
                                @puede($mov['permiso'])
                                    <a href="{{ $mov['url'] }}" class="btn btn-outline-secondary btn-sm" title="Ver"><i class="bi bi-eye"></i></a>
                                @endpuede
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">Todavía no hay movimientos registrados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
