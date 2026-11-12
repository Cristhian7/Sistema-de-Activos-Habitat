@extends('layouts.app')

@section('title', 'Historial del Equipo - Control de Activos')

@section('content')

@php
    $empleadoActual = $situacion['empleado'];
    $nombreActual = $empleadoActual
        ? trim($empleadoActual->nombre_empleado . ' ' . $empleadoActual->apellido_empleado)
        : null;

    // Color del recuadro según la situación del equipo
    $colorSituacion = match ($situacion['tipo']) {
        'asignado'    => 'primary',
        'disponible'  => 'success',
        'sin_asignar' => 'success',
        'baja'        => 'danger',
        default       => 'secondary',
    };
@endphp

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2 fw-bold text-dark"><i class="bi bi-clock-history me-2"></i>Historial del Equipo: {{ $equipo->codigo_contable }}</h1>
    <a href="{{ route('bitacora.index') }}" class="btn btn-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Volver a la Bitácora
    </a>
</div>

<div class="row g-4">
    <!-- Situación actual -->
    <div class="col-md-12">
        <div class="card border-0 shadow-sm p-4 border-start border-4 border-{{ $colorSituacion }}">
            <h5 class="fw-bold text-{{ $colorSituacion }} mb-3">
                <i class="bi bi-person-badge me-2"></i>{{ $situacion['titulo'] }}
                <span class="badge bg-{{ $colorSituacion }} ms-2 fs-6">{{ $situacion['estado'] }}</span>
            </h5>

            @if($empleadoActual)
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="text-muted small">Empleado</div>
                        <div class="fw-semibold fs-5">{{ $nombreActual }}</div>
                    </div>
                    <div class="col-md-2">
                        <div class="text-muted small">Código L4</div>
                        <div class="fw-semibold">{{ $empleadoActual->cod_l4 ?? 'N/A' }}</div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-muted small">Puesto</div>
                        <div class="fw-semibold">{{ $empleadoActual->puesto->nombre_puesto ?? 'N/A' }}</div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-muted small">Afiliado</div>
                        <div class="fw-semibold">{{ $empleadoActual->afiliado->nombre_afiliado ?? 'N/A' }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-muted small">{{ $situacion['tipo'] === 'asignado' ? 'Lo tiene desde' : 'Lo tuvo desde' }}</div>
                        <div class="fw-semibold">{{ $situacion['desde'] ? $situacion['desde']->format('d/m/Y') : 'N/A' }}</div>
                    </div>
                    @if($situacion['tipo'] === 'baja')
                        <div class="col-md-4">
                            <div class="text-muted small">Fecha de la baja</div>
                            <div class="fw-semibold">{{ $situacion['hasta'] ? $situacion['hasta']->format('d/m/Y') : 'Sin registro de baja' }}</div>
                        </div>
                    @elseif($situacion['tipo'] === 'disponible')
                        <div class="col-md-4">
                            <div class="text-muted small">Lo devolvió el</div>
                            <div class="fw-semibold">{{ $situacion['hasta'] ? $situacion['hasta']->format('d/m/Y') : 'Sin registro de devolución' }}</div>
                        </div>
                    @endif
                </div>
            @else
                <p class="mb-0 text-muted">Este equipo está disponible: todavía no se ha asignado a ningún empleado.</p>
            @endif
        </div>
    </div>

    <!-- Datos del equipo -->
    <div class="col-md-12">
        <div class="card border-0 shadow-sm p-4">
            <h5 class="fw-bold text-primary mb-3"><i class="bi bi-laptop me-2"></i>Equipo</h5>
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="text-muted small">Código Contable</div>
                    <div class="fw-semibold">{{ $equipo->codigo_contable }}</div>
                </div>
                <div class="col-md-3">
                    <div class="text-muted small">Nombre</div>
                    <div class="fw-semibold">{{ $equipo->nombre_equipo }}</div>
                </div>
                <div class="col-md-2">
                    <div class="text-muted small">Tipo</div>
                    <div class="fw-semibold">{{ $equipo->tipoEquipo->nombre_tipo ?? 'N/A' }}</div>
                </div>
                <div class="col-md-2">
                    <div class="text-muted small">Marca</div>
                    <div class="fw-semibold">{{ $equipo->marca->nombre_marca ?? 'N/A' }}</div>
                </div>
                <div class="col-md-2">
                    <div class="text-muted small">Modelo</div>
                    <div class="fw-semibold">{{ $equipo->modelo->nombre_modelo ?? 'N/A' }}</div>
                </div>
                <div class="col-md-3">
                    <div class="text-muted small">Service Tag</div>
                    <div class="fw-semibold">{{ $equipo->st ?? 'N/A' }}</div>
                </div>
                <div class="col-md-3">
                    <div class="text-muted small">Estado</div>
                    <div class="fw-semibold">
                        <span class="badge {{ $equipo->estado?->claseBadge() ?? 'bg-secondary' }}">{{ $equipo->estado->nombre_estado ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Historial de movimientos -->
    <div class="col-md-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <h5 class="fw-bold text-primary m-4 mb-3">
                    <i class="bi bi-list-ul me-2"></i>Movimientos
                    <span class="badge bg-secondary ms-1">{{ $movimientos->count() }}</span>
                </h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th class="py-3 ps-4">Fecha</th>
                                <th class="py-3">Movimiento</th>
                                <th class="py-3">Empleado</th>
                                <th class="py-3">Afiliado</th>
                                <th class="py-3">Detalle</th>
                                <th class="py-3">Técnico</th>
                                <th class="py-3 text-end pe-4">Registro</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($movimientos as $mov)
                                <tr>
                                    <td class="ps-4 fw-semibold">{{ $mov['fecha'] ? $mov['fecha']->format('d/m/Y') : 'N/A' }}</td>
                                    <td>
                                        <span class="badge {{ $mov['badge'] }}"><i class="bi {{ $mov['icono'] }} me-1"></i>{{ $mov['titulo'] }}</span>
                                    </td>
                                    <td>
                                        {{ $mov['empleado'] ? trim($mov['empleado']->nombre_empleado . ' ' . $mov['empleado']->apellido_empleado) : 'N/A' }}
                                        <div class="small text-muted">{{ $mov['rol'] }}</div>
                                    </td>
                                    <td>{{ $mov['empleado']->afiliado->nombre_afiliado ?? 'N/A' }}</td>
                                    <td style="max-width: 320px;">{{ $mov['detalle'] !== '' ? \Illuminate\Support\Str::limit($mov['detalle'], 120) : '—' }}</td>
                                    <td>
                                        @if($mov['tecnico'])
                                            {{ $mov['tecnico']->nombre_empleado }} {{ $mov['tecnico']->apellido_empleado }}
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4">
                                        <a href="{{ $mov['url'] }}" class="btn btn-outline-secondary btn-sm" title="Ver el registro de {{ strtolower($mov['titulo']) }}">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">Este equipo todavía no tiene movimientos registrados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
