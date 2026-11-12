@extends('layouts.app')

@section('title', 'Listado de Bajas - Control de Activos')

@section('content')

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2 fw-bold text-dark"><i class="bi bi-trash me-2"></i>Listado de Bajas</h1>
    @puede('bajas.crear')
    <a href="{{ route('bajas.create') }}" class="btn btn-danger btn-sm">
        <i class="bi bi-plus-lg me-1"></i> Nueva Baja
    </a>
    @endpuede
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- Aviso: bajas a las que aún les falta subir el documento firmado --}}
@php
    $pendientes = $bajas->where('tiene_documento', false)->sortBy('id_baja')->map(fn ($b) => [
        'texto' => ($b->equipo->codigo_contable ?? 'N/A') . ' — '
            . trim(($b->empleado->nombre_empleado ?? '') . ' ' . ($b->empleado->apellido_empleado ?? ''))
            . ' — baja del ' . ($b->fecha_baja ? \Carbon\Carbon::parse($b->fecha_baja)->format('d/m/Y') : 'N/A'),
        'url' => route('bajas.show', $b->id_baja),
    ])->values();
@endphp
@include('partials.alerta-documentos', ['pendientes' => $pendientes, 'singular' => 'baja', 'plural' => 'bajas', 'id' => 'pendientesBajas', 'permiso' => 'bajas'])

<!-- Tabla de Bajas -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th class="py-3 ps-3">Código Contable</th>
                        <th class="py-3">Nombre</th>
                        <th class="py-3">Tipo</th>
                        <th class="py-3">Marca</th>
                        <th class="py-3">Modelo</th>
                        <th class="py-3">Empleado</th>
                        <th class="py-3">Problema</th>
                        <th class="py-3">Tiempo de Uso</th>
                        <th class="py-3">Reemplazo</th>
                        <th class="py-3">Solicitud de Equipo</th>
                        <th class="py-3">Fecha de Baja</th>
                        <th class="py-3">Técnico</th>
                        <th class="py-3">Documento firmado</th>
                        <th class="py-3 text-end pe-3">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bajas as $baja)
                    <tr>
                        <td class="ps-3 fw-semibold">{{ $baja->equipo->codigo_contable ?? 'N/A' }}</td>
                        <td>{{ $baja->equipo->nombre_equipo ?? 'N/A' }}</td>
                        <td>{{ $baja->equipo->tipoEquipo->nombre_tipo ?? 'N/A' }}</td>
                        <td>{{ $baja->equipo->marca->nombre_marca ?? 'N/A' }}</td>
                        <td>{{ $baja->equipo->modelo->nombre_modelo ?? 'N/A' }}</td>
                        <td>{{ $baja->empleado->nombre_empleado ?? 'N/A' }} {{ $baja->empleado->apellido_empleado ?? '' }}</td>
                        <td>{{ Str::limit($baja->problema, 40) }}</td>
                        <td>{{ $baja->tiempo_uso }}</td>
                        <td>
                            @if($baja->necesita_reemplazo === 'SI')
                                <span class="badge bg-warning text-dark">Sí</span>
                            @elseif($baja->necesita_reemplazo === 'NO')
                                <span class="badge bg-secondary">No</span>
                            @else
                                N/A
                            @endif
                        </td>
                        <td>
                            {{-- Solo las bajas con reemplazo "Sí" llevan la solicitud de equipo nuevo --}}
                            @if($baja->necesita_reemplazo === 'SI')
                                <a href="{{ route('bajas.show', $baja->id_baja) }}" class="fw-semibold text-primary text-decoration-none" title="Ver la solicitud">
                                    Solicitar equipo nuevo
                                </a>
                            @endif
                        </td>
                        <td>{{ $baja->fecha_baja ? \Carbon\Carbon::parse($baja->fecha_baja)->format('d/m/Y') : 'N/A' }}</td>
                        <td class="fw-semibold">
                            @if($baja->tecnico)
                                {{ $baja->tecnico->nombre_empleado }} {{ $baja->tecnico->apellido_empleado }}
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td>
                            @if($baja->tiene_documento)
                                <a href="{{ route('bajas.documento.ver', $baja->id_baja) }}" target="_blank" class="badge bg-success text-decoration-none" title="Ver el documento firmado">
                                    <i class="bi bi-file-earmark-check me-1"></i>Cargado
                                </a>
                            @else
                                <a href="{{ route('bajas.show', $baja->id_baja) }}#documento-firmado" class="badge bg-warning text-dark text-decoration-none" title="Falta subir el documento firmado">
                                    <i class="bi bi-exclamation-triangle me-1"></i>Pendiente
                                </a>
                            @endif
                        </td>
                        <td class="text-end pe-3">
                            <a href="{{ route('bajas.show', $baja->id_baja) }}" class="btn btn-outline-secondary btn-sm" title="Ver">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('bajas.imprimir', $baja->id_baja) }}" class="btn btn-outline-success btn-sm" title="Imprimir" target="_blank">
                                <i class="bi bi-printer"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="14" class="text-center text-muted py-4">No hay bajas registradas actualmente.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
