@extends('layouts.app')

@section('title', 'Listado de Traslados - Control de Activos')

@section('content')

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2 fw-bold text-dark"><i class="bi bi-arrow-left-right me-2"></i>Listado de Traslados</h1>
    <a href="{{ route('traslados.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i> Nuevo Traslado
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Tabla de Traslados -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <!--<th class="py-3 ps-3">ID</th>!-->
                        <th class="py-3">Código</th>
                        <th class="py-3">Tipo</th>
                        <th class="py-3">Marca</th>
                        <th class="py-3">Modelo</th>
                        <th class="py-3">Empleado</th>
                        <th class="py-3">Apellido</th>
                        <th class="py-3">Puesto</th>
                        <th class="py-3">Afiliado</th>
                        <th class="py-3">Fecha de Traslado</th>
                        <th class="py-3 text-end pe-3">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($traslados as $traslado)
                    <tr>
                        <!--<td class="ps-3 fw-semibold">{{ $traslado->id_traslado }}</td>-->
                        <td class="codigo-equipo ps-3 fw-semibold">{{ $equipo->codigo_contable }}</td>
                       <!-- <td>{{ $traslado->equipo->nombre_equipo ?? 'Equipo no disponible' }}</td>
                        <td>{{ $traslado->empleadoDestino->nombre_empleado ?? '' }} {{ $traslado->empleadoDestino->apellido_empleado ?? '' }}</td>
                        <td>{{ $traslado->fecha_traslado }}</td>
                        <td>{{ $traslado->observaciones ?? 'Sin observaciones' }}</td> -->
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">No hay traslados registrados actualmente.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection