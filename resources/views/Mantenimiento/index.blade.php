@extends('layouts.app')

@section('title', 'Listado de Mantenimientos')

@section('content')

@vite('resources/js/buscar-mantenimiento.js')

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-dark"><i class="bi bi-tools me-2"></i>Historial de Mantenimientos</h2>
        @puede('mantenimientos.crear')
        <a href="{{ route('mantenimientos.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Registrar Mantenimiento
        </a>
        @endpuede
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Barra de Búsqueda Interactiva -->
    <form action="{{ route('mantenimientos.index') }}" method="GET" style="margin-bottom: 0.5rem; display: flex; gap: 0.5rem; flex-wrap: wrap;" onsubmit="return false;">
        <input type="text" id="searchInput" name="search" placeholder="Buscar por código contable, fecha, empleado o técnico" value="{{ request('search') }}" autocomplete="off"
               style="flex: 1; min-width: 250px; padding: 0.5rem 0.75rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.875rem;">
    </form>
    <div id="contadorResultados" class="small text-muted mb-3" style="min-height: 1.25rem;"></div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <!-- <th>ID</th> -->
                            <th>Código Equipo</th>
                            <th>Nombre Equipo</th>
                            <th>Empleado</th>
                            <th>Descripción</th>
                            <th>Piezas</th>
                            <th>Fecha</th>
                            <th>Técnico</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($mantenimientos as $mantenimiento)
                            @php
                                $nombreEmpleado = trim(($mantenimiento->empleado->nombre_empleado ?? '') . ' ' . ($mantenimiento->empleado->apellido_empleado ?? ''));
                                $nombreTecnico = $mantenimiento->tecnico
                                    ? trim($mantenimiento->tecnico->nombre_empleado . ' ' . $mantenimiento->tecnico->apellido_empleado)
                                    : '';
                                // La fecha se puede buscar como 2026-11-06 o como 06/11/2026
                                $fechaBusqueda = $mantenimiento->fecha_mantenimiento
                                    ? $mantenimiento->fecha_mantenimiento . ' ' . \Carbon\Carbon::parse($mantenimiento->fecha_mantenimiento)->format('d/m/Y')
                                    : '';
                            @endphp
                            <tr class="fila-mantenimiento"
                                data-codigo="{{ Str::lower($mantenimiento->equipo->codigo_contable ?? '') }}"
                                data-fecha="{{ $fechaBusqueda }}"
                                data-empleado="{{ Str::lower($nombreEmpleado) }}"
                                data-tecnico="{{ Str::lower($nombreTecnico) }}">
                                <!--<td>{{ $mantenimiento->id_mantenimiento }}</td> -->
                                <td><span class="badge bg-secondary">{{ $mantenimiento->equipo->codigo_contable ?? 'N/A' }}</span></td>
                                <td>{{ $mantenimiento->equipo->nombre_equipo ?? 'N/A' }}</td>
                                <td>{{ $mantenimiento->empleado->nombre_empleado ?? 'N/A' }} {{ $mantenimiento->empleado->apellido_empleado ?? '' }}</td>
                                <td>{{ Str::limit($mantenimiento->descripcion, 50) }}</td>
                                <td>
                                    @if($mantenimiento->piezas->isNotEmpty())
                                        <span class="badge bg-info text-dark" title="{{ $mantenimiento->piezas->pluck('resumen')->implode(', ') }}">
                                            <i class="bi bi-motherboard me-1"></i>{{ $mantenimiento->piezas->count() }}
                                        </span>
                                        <div class="small text-muted">{{ Str::limit($mantenimiento->piezas->pluck('tipo_pieza')->unique()->implode(', '), 40) }}</div>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>{{ $mantenimiento->fecha_mantenimiento }}</td>
                                <td class="fw-semibold">
                                    @if($mantenimiento->tecnico)
                                        {{ $mantenimiento->tecnico->nombre_empleado }} {{ $mantenimiento->tecnico->apellido_empleado }}
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('mantenimientos.show', $mantenimiento->id_mantenimiento) }}" class="btn btn-outline-secondary btn-sm" title="Ver">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('mantenimientos.imprimir', $mantenimiento->id_mantenimiento) }}" class="btn btn-outline-success btn-sm" title="Imprimir" target="_blank" rel="opener">
                                        <i class="bi bi-printer"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">No hay mantenimientos registrados.</td>
                            </tr>
                        @endforelse
                        <!-- Fila mostrada cuando el filtro no encuentra coincidencias -->
                        <tr id="filaSinResultados" style="display: none;">
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="bi bi-search me-2"></i>No se encontraron mantenimientos que coincidan con la búsqueda.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection