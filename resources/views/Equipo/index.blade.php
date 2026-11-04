@extends('layouts.app')

@section('title', 'Catálogo de Equipos - Control de Activos')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2 fw-bold text-dark"><i class="bi bi-laptop me-2"></i>Catálogo de Equipos</h1>
    <a href="{{ route('equipos.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Nuevo Equipo
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Código Contable</th>
                        <th>Nombre</th>
                        <th>Tipo</th>
                        <th>Marca</th>
                        <th>Modelo</th>
                        <th>Especificaciones</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($equipos as $equipo)
                        <tr>
                            <td class="fw-semibold">{{ $equipo->codigo_contable }}</td>
                            <td>{{ $equipo->nombre_equipo }}</td>
                            <td>{{ $equipo->tipoEquipo->nombre_tipo ?? 'N/A' }}</td>
                            <td>{{ $equipo->marca->nombre_marca ?? 'N/A' }}</td>
                            <td>{{ $equipo->modelo->nombre_modelo ?? 'N/A' }}</td>
                            <td>
                                <small class="text-muted d-block"><i class="bi bi-cpu me-1"></i>{{ $equipo->procesador ?? 'N/A' }}</small>
                                <small class="text-muted"><i class="bi bi-memory me-1"></i>{{ $equipo->ram ?? 'N/A' }}</small>
                            </td>
                            <td>
                                @if($equipo->estado_id_estado == 2)
                                    <span class="badge bg-danger">{{ $equipo->estado->nombre_estado ?? 'Baja' }}</span>
                                @else
                                    <span class="badge bg-success">{{ $equipo->estado->nombre_estado ?? 'Activo' }}</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('equipos.edit', $equipo->id_equipo) }}" class="btn btn-sm btn-outline-primary" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('equipos.destroy', $equipo->id_equipo) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de dar de baja este equipo?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Dar de baja">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                No hay equipos registrados todavía. Comienza agregando uno nuevo con el botón superior.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection