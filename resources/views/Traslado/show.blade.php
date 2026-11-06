@extends('layouts.app')

@section('title', 'Detalle del Traslado - Control de Activos')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2 fw-bold text-dark"><i class="bi bi-arrow-left-right me-2"></i>Detalle del Traslado</h1>
    <a href="{{ route('traslados.index') }}" class="btn btn-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Volver al Listado
    </a>
</div>

<div class="row g-4">
    <!-- Datos del Traslado -->
    <div class="col-md-12">
        <div class="card border-0 shadow-sm p-4">
            <h5 class="fw-bold text-primary mb-3"><i class="bi bi-info-circle me-2"></i>Información del Traslado</h5>
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="text-muted small">Fecha de Envío</div>
                    <div class="fw-semibold">{{ $traslado->fecha_envio ?? 'N/A' }}</div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">Motivo del Traslado</div>
                    <div class="fw-semibold">{{ $traslado->motivo_traslado ?? 'N/A' }}</div>
                </div>
                <div class="col-md-12">
                    <div class="text-muted small">Descripción / Observaciones</div>
                    <div class="fw-semibold">{{ $traslado->descripcion ?? 'N/A' }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Datos del Empleado -->
    <div class="col-md-12">
        <div class="card border-0 shadow-sm p-4">
            <h5 class="fw-bold text-primary mb-3"><i class="bi bi-person me-2"></i>Empleado que Recibe</h5>
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="text-muted small">Nombre</div>
                    <div class="fw-semibold">{{ $traslado->empleado->nombre_empleado ?? 'N/A' }} {{ $traslado->empleado->apellido_empleado ?? '' }}</div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">Código L4</div>
                    <div class="fw-semibold">{{ $traslado->empleado->cod_l4 ?? 'N/A' }}</div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">Puesto</div>
                    <div class="fw-semibold">{{ $traslado->empleado->puesto->nombre_puesto ?? 'N/A' }}</div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">Afiliado</div>
                    <div class="fw-semibold">{{ $traslado->empleado->afiliado->nombre_afiliado ?? 'N/A' }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Datos del Equipo -->
    <div class="col-md-12">
        <div class="card border-0 shadow-sm p-4">
            <h5 class="fw-bold text-primary mb-3"><i class="bi bi-laptop me-2"></i>Equipo Trasladado</h5>
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="text-muted small">Código Contable</div>
                    <div class="fw-semibold">{{ $traslado->equipo->codigo_contable ?? 'N/A' }}</div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">Nombre del Equipo</div>
                    <div class="fw-semibold">{{ $traslado->equipo->nombre_equipo ?? 'N/A' }}</div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">Tipo</div>
                    <div class="fw-semibold">{{ $traslado->equipo->tipoEquipo->nombre_tipo ?? 'N/A' }}</div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">Marca</div>
                    <div class="fw-semibold">{{ $traslado->equipo->marca->nombre_marca ?? 'N/A' }}</div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">Modelo</div>
                    <div class="fw-semibold">{{ $traslado->equipo->modelo->nombre_modelo ?? 'N/A' }}</div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">Estado</div>
                    <div class="fw-semibold">{{ $traslado->equipo->estado->nombre_estado ?? 'N/A' }}</div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">Procesador</div>
                    <div class="fw-semibold">{{ $traslado->equipo->procesador ?? 'N/A' }}</div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">RAM</div>
                    <div class="fw-semibold">{{ $traslado->equipo->ram ?? 'N/A' }} ({{ $traslado->equipo->tipo_ram ?? 'N/A' }})</div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">SSD</div>
                    <div class="fw-semibold">{{ $traslado->equipo->ssd ?? 'N/A' }}</div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">Service Tag</div>
                    <div class="fw-semibold">{{ $traslado->equipo->st ?? 'N/A' }}</div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">Express Code</div>
                    <div class="fw-semibold">{{ $traslado->equipo->ex ?? 'N/A' }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Imágenes del Equipo -->
    <div class="col-md-12">
        <div class="card border-0 shadow-sm p-4">
            <h5 class="fw-bold text-primary mb-3"><i class="bi bi-images me-2"></i>Imágenes del Equipo</h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="text-muted small mb-1">Fotografía del Equipo</div>
                    @if(!empty($traslado->equipo->imagen_equipo))
                        <div class="text-center p-2 border rounded bg-light">
                            <img src="data:image/jpeg;base64,{{ base64_encode($traslado->equipo->imagen_equipo) }}"
                                 alt="Fotografía del Equipo"
                                 class="img-fluid rounded"
                                 style="width: 100%; max-height: 350px; object-fit: contain;">
                        </div>
                    @else
                        <span class="badge bg-secondary">Sin imagen registrada</span>
                    @endif
                </div>
                <div class="col-md-6">
                    <div class="text-muted small mb-1">Fotografía del Código Contable</div>
                    @if(!empty($traslado->equipo->imagen_codigo))
                        <div class="text-center p-2 border rounded bg-light">
                            <img src="data:image/jpeg;base64,{{ base64_encode($traslado->equipo->imagen_codigo) }}"
                                 alt="Fotografía del Código Contable"
                                 class="img-fluid rounded"
                                 style="max-height: 150px; object-fit: contain;">
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
    <a href="{{ route('traslados.imprimir', $traslado->id_traslado) }}" class="btn btn-success px-4 py-2 fw-semibold" target="_blank">
        <i class="bi bi-printer me-1"></i> Imprimir
    </a>
    <a href="{{ route('traslados.edit', $traslado->id_traslado) }}" class="btn btn-primary px-4 py-2 fw-semibold">
        <i class="bi bi-pencil me-1"></i> Editar Traslado
    </a>
</div>
@endsection
