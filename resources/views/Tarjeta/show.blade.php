@extends('layouts.app')

@section('title', 'Tarjeta de Responsabilidad - Control de Activos')

@section('content')

@php
    $nombre = trim($empleado->nombre_empleado . ' ' . $empleado->apellido_empleado);
@endphp

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2 fw-bold text-dark"><i class="bi bi-person-vcard me-2"></i>Tarjeta de Responsabilidad</h1>
    <a href="{{ route('tarjetas.index') }}" class="btn btn-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Volver
    </a>
</div>

<div class="row g-4">
    <!-- Datos del empleado -->
    <div class="col-md-12">
        <div class="card border-0 shadow-sm p-4">
            <h5 class="fw-bold text-primary mb-3"><i class="bi bi-person me-2"></i>Empleado</h5>
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="text-muted small">Nombre</div>
                    <div class="fw-semibold fs-5">{{ $nombre }}</div>
                </div>
                <div class="col-md-2">
                    <div class="text-muted small">Código L4</div>
                    <div class="fw-semibold">{{ $empleado->cod_l4 ?? 'N/A' }}</div>
                </div>
                <div class="col-md-3">
                    <div class="text-muted small">Puesto</div>
                    <div class="fw-semibold">{{ $empleado->puesto->nombre_puesto ?? 'N/A' }}</div>
                </div>
                <div class="col-md-2">
                    <div class="text-muted small">Afiliado</div>
                    <div class="fw-semibold">{{ $empleado->afiliado->nombre_afiliado ?? 'N/A' }}</div>
                </div>
                <div class="col-md-1">
                    <div class="text-muted small">Estado</div>
                    <span class="badge {{ $empleado->estado?->claseBadge() ?? 'bg-secondary' }}">{{ $empleado->estado->nombre_estado ?? 'N/A' }}</span>
                </div>
            </div>
        </div>
    </div>

    @if($sinDevolver->isNotEmpty())
        <div class="col-md-12">
            <div class="alert alert-warning mb-0">
                <i class="bi bi-exclamation-triangle-fill me-1"></i>
                Este empleado fue dado de baja, pero aún figuran como entregados:
                <strong>{{ $sinDevolver->map(fn ($t) => $t->equipo->codigo_contable)->implode(', ') }}</strong>.
                Regularícelo con el botón <em>Devolver</em> en el módulo de
                <a href="{{ route('traslados.index') }}" class="alert-link">Traslados</a>.
            </div>
        </div>
    @endif

    <!-- Equipos bajo su responsabilidad -->
    <div class="col-md-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <h5 class="fw-bold text-primary m-4 mb-3">
                    <i class="bi bi-laptop me-2"></i>Equipos bajo su responsabilidad
                    <span class="badge bg-primary ms-1">{{ $aCargo->count() }}</span>
                </h5>

                @if($aCargo->isEmpty())
                    <p class="text-muted px-4 pb-4 mb-0">
                        Este empleado no tiene equipos bajo su responsabilidad actualmente.
                    </p>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th class="py-3 ps-4">Código Contable</th>
                                    <th class="py-3">Equipo</th>
                                    <th class="py-3">Marca</th>
                                    <th class="py-3">Modelo</th>
                                    <th class="py-3">Service Tag</th>
                                    <th class="py-3">Entregado el</th>
                                    <th class="py-3">Valor en Libros</th>
                                    <th class="py-3 text-end pe-4">Traslado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($aCargo as $traslado)
                                    @php $equipo = $traslado->equipo; @endphp
                                    <tr>
                                        <td class="ps-4 fw-semibold">{{ $equipo->codigo_contable }}</td>
                                        <td>{{ $equipo->tipoEquipo->nombre_tipo ?? 'N/A' }}</td>
                                        <td>{{ $equipo->marca->nombre_marca ?? 'N/A' }}</td>
                                        <td>{{ $equipo->modelo->nombre_modelo ?? 'N/A' }}</td>
                                        <td>{{ $equipo->st ?? 'N/A' }}</td>
                                        <td>{{ \Carbon\Carbon::parse($traslado->fecha_envio)->format('d/m/Y') }}</td>
                                        <td>{{ $equipo->valor_libros !== null ? 'Q ' . number_format((float) $equipo->valor_libros, 2) : 'N/A' }}</td>
                                        <td class="text-end pe-4">
                                            <a href="{{ route('traslados.show', $traslado->id_traslado) }}" class="btn btn-outline-secondary btn-sm" title="Ver el traslado">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="mt-4 mb-5 text-end">
    <a href="{{ route('tarjetas.imprimir', $empleado->id_empleado) }}" class="btn btn-success px-4 py-2 fw-semibold" target="_blank">
        <i class="bi bi-printer me-1"></i> Imprimir tarjeta
    </a>
</div>
@endsection
