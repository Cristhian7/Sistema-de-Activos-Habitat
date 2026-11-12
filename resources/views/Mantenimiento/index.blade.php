@extends('layouts.app')

@section('title', 'Listado de Mantenimientos')

@section('content')
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
                            <th>Fecha</th>
                            <th>Técnico</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($mantenimientos as $mantenimiento)
                            <tr>
                                <!--<td>{{ $mantenimiento->id_mantenimiento }}</td> -->
                                <td><span class="badge bg-secondary">{{ $mantenimiento->equipo->codigo_contable ?? 'N/A' }}</span></td>
                                <td>{{ $mantenimiento->equipo->nombre_equipo ?? 'N/A' }}</td>
                                <td>{{ $mantenimiento->empleado->nombre_empleado ?? 'N/A' }} {{ $mantenimiento->empleado->apellido_empleado ?? '' }}</td>
                                <td>{{ Str::limit($mantenimiento->descripcion, 50) }}</td>
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
                                    <a href="{{ route('mantenimientos.imprimir', $mantenimiento->id_mantenimiento) }}" class="btn btn-outline-success btn-sm" title="Imprimir" target="_blank">
                                        <i class="bi bi-printer"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">No hay mantenimientos registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $mantenimientos->links() }}
            </div>
        </div>
    </div>
</div>
@endsection