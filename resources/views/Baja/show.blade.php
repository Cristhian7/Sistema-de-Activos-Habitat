@extends('layouts.app')

@section('title', 'Detalle de la Baja - Control de Activos')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2 fw-bold text-dark"><i class="bi bi-trash me-2"></i>Detalle de la Baja</h1>
    <a href="{{ route('bajas.index') }}" class="btn btn-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Volver al Listado
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@include('partials.revision-notificacion', ['tipo' => 'baja', 'registroId' => $baja->id_baja])

<div class="mb-4">
    @include('partials.documento-firmado', ['registro' => $baja, 'tipo' => 'bajas'])
</div>

<div class="row g-4">
    <!-- Datos de la Baja -->
    <div class="col-md-12">
        <div class="card border-0 shadow-sm p-4">
            <h5 class="fw-bold text-danger mb-3"><i class="bi bi-info-circle me-2"></i>Información de la Baja</h5>
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="text-muted small">Fecha de Baja</div>
                    <div class="fw-semibold">{{ $baja->fecha_baja ? \Carbon\Carbon::parse($baja->fecha_baja)->format('d/m/Y') : 'N/A' }}</div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">Tiempo de Uso</div>
                    <div class="fw-semibold">{{ $baja->tiempo_uso }}</div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">¿Necesita Reemplazo?</div>
                    <div class="fw-semibold">{{ $baja->necesita_reemplazo === 'SI' ? 'Sí' : ($baja->necesita_reemplazo === 'NO' ? 'No' : 'N/A') }}</div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">Técnico que realizó la baja</div>
                    <div class="fw-semibold">
                        @if($baja->tecnico)
                            {{ $baja->tecnico->nombre_empleado }} {{ $baja->tecnico->apellido_empleado }}
                        @else
                            N/A
                        @endif
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="text-muted small">Problema</div>
                    <div class="fw-semibold">{{ $baja->problema ?? 'N/A' }}</div>
                </div>
                <div class="col-md-12">
                    <div class="text-muted small">Detalle / Motivo</div>
                    <div class="fw-semibold" style="white-space: pre-line;">{{ $baja->detalle ?? 'N/A' }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Empleado -->
    <div class="col-md-12">
        <div class="card border-0 shadow-sm p-4">
            <h5 class="fw-bold text-primary mb-3"><i class="bi bi-person me-2"></i>Empleado que tenía el equipo</h5>
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="text-muted small">Nombre</div>
                    <div class="fw-semibold">{{ $baja->empleado->nombre_empleado ?? 'N/A' }} {{ $baja->empleado->apellido_empleado ?? '' }}</div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">Puesto</div>
                    <div class="fw-semibold">{{ $baja->empleado->puesto->nombre_puesto ?? 'N/A' }}</div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">Afiliado</div>
                    <div class="fw-semibold">{{ $baja->empleado->afiliado->nombre_afiliado ?? 'N/A' }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Equipo -->
    <div class="col-md-12">
        <div class="card border-0 shadow-sm p-4">
            <h5 class="fw-bold text-primary mb-3"><i class="bi bi-laptop me-2"></i>Equipo dado de baja</h5>
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="text-muted small">Código Contable</div>
                    <div class="fw-semibold">{{ $baja->equipo->codigo_contable ?? 'N/A' }}</div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">Nombre del Equipo</div>
                    <div class="fw-semibold">{{ $baja->equipo->nombre_equipo ?? 'N/A' }}</div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">Estado</div>
                    <div class="fw-semibold"><span class="badge {{ $baja->equipo->estado?->claseBadge() ?? 'bg-secondary' }}">{{ $baja->equipo->estado->nombre_estado ?? 'N/A' }}</span></div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">Tipo</div>
                    <div class="fw-semibold">{{ $baja->equipo->tipoEquipo->nombre_tipo ?? 'N/A' }}</div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">Marca</div>
                    <div class="fw-semibold">{{ $baja->equipo->marca->nombre_marca ?? 'N/A' }}</div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">Modelo</div>
                    <div class="fw-semibold">{{ $baja->equipo->modelo->nombre_modelo ?? 'N/A' }}</div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">Procesador</div>
                    <div class="fw-semibold">{{ $baja->equipo->procesador ?? 'N/A' }}</div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">RAM</div>
                    <div class="fw-semibold">{{ $baja->equipo->ram ?? 'N/A' }} ({{ $baja->equipo->tipo_ram ?? 'N/A' }})</div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">SSD</div>
                    <div class="fw-semibold">{{ $baja->equipo->ssd ?? 'N/A' }}</div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">Service Tag</div>
                    <div class="fw-semibold">{{ $baja->equipo->st ?? 'N/A' }}</div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">Valor en Libros</div>
                    <div class="fw-semibold">{{ $baja->equipo->valor_libros ?? 'N/A' }}</div>
                </div>
            </div>
        </div>
    </div>

    @if($baja->necesita_reemplazo === 'SI')
    <!-- Solicitud de equipo nuevo (solo si la baja necesita reemplazo) -->
    <div class="col-md-12">
        <div class="card border-0 shadow-sm p-4">
            <h5 class="fw-bold text-primary mb-3"><i class="bi bi-box-seam me-2"></i>Solicitud de Equipo Nuevo</h5>
            <div class="table-responsive">
                @include('baja._solicitud', ['baja' => $baja])
            </div>
        </div>
    </div>
    @endif

    <!-- Imágenes del equipo -->
    <div class="col-md-12">
        <div class="card border-0 shadow-sm p-4">
            <h5 class="fw-bold text-primary mb-3"><i class="bi bi-images me-2"></i>Imágenes del Equipo</h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="text-muted small mb-1">Fotografía del Equipo</div>
                    @if(!empty($baja->equipo->imagen_equipo))
                        <div class="text-center p-2 border rounded bg-light">
                            <img src="data:image/jpeg;base64,{{ base64_encode($baja->equipo->imagen_equipo) }}"
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
                    @if(!empty($baja->equipo->imagen_codigo))
                        <div class="text-center p-2 border rounded bg-light">
                            <img src="data:image/jpeg;base64,{{ base64_encode($baja->equipo->imagen_codigo) }}"
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
    <a href="{{ route('bajas.imprimir', $baja->id_baja) }}" class="btn btn-success px-4 py-2 fw-semibold" target="_blank">
        <i class="bi bi-printer me-1"></i> Imprimir
    </a>
</div>
@endsection
