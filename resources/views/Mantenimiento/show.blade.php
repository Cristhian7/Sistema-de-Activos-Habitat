@extends('layouts.app')

@section('title', 'Detalle del Mantenimiento - Control de Activos')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2 fw-bold text-dark"><i class="bi bi-tools me-2"></i>Detalle del Mantenimiento</h1>
    <a href="{{ route('mantenimientos.index') }}" class="btn btn-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Volver al Listado
    </a>
</div>

<div class="row g-4">
    <!-- Datos del Mantenimiento -->
    <div class="col-md-12">
        <div class="card border-0 shadow-sm p-4">
            <h5 class="fw-bold text-primary mb-3"><i class="bi bi-info-circle me-2"></i>Información del Mantenimiento</h5>
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="text-muted small">Fecha de Mantenimiento</div>
                    <div class="fw-semibold">{{ \Carbon\Carbon::parse($mantenimiento->fecha_mantenimiento)->format('d/m/Y') }}</div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">Técnico que realizó el mantenimiento</div>
                    <div class="fw-semibold">
                        @if($mantenimiento->tecnico)
                            {{ $mantenimiento->tecnico->nombre_empleado }} {{ $mantenimiento->tecnico->apellido_empleado }}
                        @else
                            N/A
                        @endif
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">Empleado</div>
                    <div class="fw-semibold">{{ $mantenimiento->empleado->nombre_empleado ?? 'N/A' }} {{ $mantenimiento->empleado->apellido_empleado ?? '' }}</div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">Afiliado del Empleado</div>
                    <div class="fw-semibold">{{ $mantenimiento->empleado->afiliado->nombre_afiliado ?? 'N/A' }}</div>
                </div>
                <div class="col-md-12">
                    <div class="text-muted small">Descripción</div>
                    <div class="fw-semibold">{{ $mantenimiento->descripcion ?? 'N/A' }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Datos del Equipo -->
    <div class="col-md-12">
        <div class="card border-0 shadow-sm p-4">
            <h5 class="fw-bold text-primary mb-3"><i class="bi bi-laptop me-2"></i>Equipo</h5>
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="text-muted small">Código Contable</div>
                    <div class="fw-semibold">{{ $mantenimiento->equipo->codigo_contable ?? 'N/A' }}</div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">Nombre del Equipo</div>
                    <div class="fw-semibold">{{ $mantenimiento->equipo->nombre_equipo ?? 'N/A' }}</div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">Tipo</div>
                    <div class="fw-semibold">{{ $mantenimiento->equipo->tipoEquipo->nombre_tipo ?? 'N/A' }}</div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">Marca</div>
                    <div class="fw-semibold">{{ $mantenimiento->equipo->marca->nombre_marca ?? 'N/A' }}</div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">Modelo</div>
                    <div class="fw-semibold">{{ $mantenimiento->equipo->modelo->nombre_modelo ?? 'N/A' }}</div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">Service Tag</div>
                    <div class="fw-semibold">{{ $mantenimiento->equipo->st ?? 'N/A' }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Piezas cambiadas o agregadas -->
    <div class="col-md-12">
        <div class="card border-0 shadow-sm p-4">
            <h5 class="fw-bold text-primary mb-3"><i class="bi bi-motherboard me-2"></i>Piezas Cambiadas o Agregadas</h5>
            @if($mantenimiento->piezas->isEmpty())
                <span class="text-muted">No se registraron piezas en este mantenimiento.</span>
            @else
                <div class="row g-3">
                    @foreach($mantenimiento->piezas as $pieza)
                        <div class="col-md-6 col-xl-4">
                            <div class="border rounded p-3 h-100">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <strong>{{ $pieza->tipo_pieza }}</strong>
                                    <span class="badge {{ $pieza->accion === 'AGREGADO' ? 'bg-success' : 'bg-warning text-dark' }}">{{ $pieza->accion_texto }}</span>
                                </div>
                                @if($pieza->descripcion)
                                    <div class="small text-muted mb-2">{{ $pieza->descripcion }}</div>
                                @endif
                                @php $urlFoto = route('mantenimientos.pieza.imagen', [$mantenimiento->id_mantenimiento, $pieza->id_mantenimiento_pieza]); @endphp
                                <a href="{{ $urlFoto }}" target="_blank" title="Ver la fotografía completa">
                                    <img src="{{ $urlFoto }}" alt="Fotografía: {{ $pieza->tipo_pieza }}" loading="lazy"
                                         class="img-fluid rounded border bg-light"
                                         style="width: 100%; max-height: 220px; object-fit: contain;">
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Fotografías del Mantenimiento -->
    <div class="col-md-12">
        <div class="card border-0 shadow-sm p-4">
            <h5 class="fw-bold text-primary mb-3"><i class="bi bi-images me-2"></i>Fotografías del Mantenimiento</h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="text-muted small mb-1">Fotografía 1</div>
                    @if(!empty($mantenimiento->imagen_mantenimiento_1))
                        <div class="text-center p-2 border rounded bg-light">
                            <img src="data:image/jpeg;base64,{{ base64_encode($mantenimiento->imagen_mantenimiento_1) }}"
                                 alt="Fotografía 1 del mantenimiento"
                                 class="img-fluid rounded"
                                 style="width: 100%; max-height: 350px; object-fit: contain;">
                        </div>
                    @else
                        <span class="badge bg-secondary">Sin imagen registrada</span>
                    @endif
                </div>
                <div class="col-md-6">
                    <div class="text-muted small mb-1">Fotografía 2</div>
                    @if(!empty($mantenimiento->imagen_mantenimiento_2))
                        <div class="text-center p-2 border rounded bg-light">
                            <img src="data:image/jpeg;base64,{{ base64_encode($mantenimiento->imagen_mantenimiento_2) }}"
                                 alt="Fotografía 2 del mantenimiento"
                                 class="img-fluid rounded"
                                 style="width: 100%; max-height: 350px; object-fit: contain;">
                        </div>
                    @else
                        <span class="badge bg-secondary">Sin imagen registrada</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-4 mb-5 text-end">
    <a href="{{ route('mantenimientos.imprimir', $mantenimiento->id_mantenimiento) }}" class="btn btn-success px-4 py-2 fw-semibold" target="_blank" rel="opener">
        <i class="bi bi-printer me-1"></i> Imprimir
    </a>
</div>
@endsection
