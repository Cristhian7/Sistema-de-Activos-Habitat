@extends('layouts.app')

@section('title', 'Listado de Equipos - Control de Activos')

@section('content')


<!-- Cargando el script de búsqueda mediante Vite -->
@vite(['resources/js/buscar-equipo.js'])


<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2 fw-bold text-dark"><i class="bi bi-laptop me-2"></i>Listado de Equipos</h1>
    @puede('equipos.crear')
    <a href="{{ route('equipos.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i> Nuevo Equipo
    </a>
    @endpuede
</div>

<!-- Alerta de éxito si existe -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Barra de Búsqueda Interactiva -->
<form action="{{ route('equipos.index') }}" method="GET" style="margin-bottom: 1.5rem; display: flex; gap: 0.5rem; flex-wrap: wrap;" onsubmit="return false;">
    <input type="text" id="searchInput" name="search" placeholder="Buscar por código contable o nombre del equipo..." value="{{ request('search') }}"
           style="flex: 1; min-width: 250px; padding: 0.5rem 0.75rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.875rem;">
</form>

<!-- Tabla de Equipos -->
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
                        <th class="py-3">Especificaciones</th>
                        <th class="py-3">Estado</th>
                        <th class="py-3">Asignación</th>
                        @puede('equipos.editar', 'equipos.eliminar')
                        <th class="py-3 text-end pe-3">Acciones</th>
                        @endpuede
                    </tr>
                </thead>
                <tbody>
                    @forelse($equipos as $equipo)
                    <!-- Fila con la clase requerida para el buscador JS -->
                    <tr class="equipo-row">
                        <td class="codigo-equipo ps-3 fw-semibold">{{ $equipo->codigo_contable }}</td>
                        <td class="nombre-equipo text-dark fw-medium">{{ $equipo->nombre_equipo }}</td>
                        <td>{{ $equipo->tipoEquipo->nombre_tipo ?? 'N/A' }}</td>
                        <td>{{ $equipo->marca->nombre_marca ?? 'N/A' }}</td>
                        <td>{{ $equipo->modelo->nombre_modelo ?? 'N/A' }}</td>
                        <td>
                            <div class="small text-muted">
                                @if($equipo->procesador) <i class="bi bi-cpu me-1"></i>{{ $equipo->procesador }} @endif
                                @if($equipo->ram) <br><i class="bi bi-memory me-1"></i>{{ $equipo->ram }} @endif
                            </div>
                        </td>
                        <td>
                            <span class="badge {{ $equipo->estado?->claseBadge() ?? 'bg-secondary' }}">{{ $equipo->estado->nombre_estado ?? 'N/A' }}</span>
                        </td>
                        <td>
                            {{-- Disponibilidad para asignar: dado de baja = no disponible; con traslado vigente = asignado; sin traslado vigente = disponible --}}
                            @if(\App\Models\Estado\Estado::esBaja($equipo->estado_id_estado))
                                <span class="badge bg-secondary" title="El equipo fue dado de baja y no se puede asignar">No disponible</span>
                            @elseif($equipo->asignacionActual)
                                <span class="badge bg-warning text-dark">Asignado</span>
                                <div class="small text-muted mt-1">
                                    {{ $equipo->asignacionActual->empleado->nombre_empleado ?? '' }} {{ $equipo->asignacionActual->empleado->apellido_empleado ?? '' }}
                                </div>
                            @else
                                <span class="badge bg-success" title="Se puede asignar a un empleado">Disponible</span>
                            @endif
                        </td>
                        @puede('equipos.editar', 'equipos.eliminar')
                        <td class="text-end pe-3">
                            @puede('equipos.editar')
                            <a href="{{ route('equipos.edit', $equipo->id_equipo) }}" class="btn btn-outline-primary btn-sm" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            @endpuede
                            @puede('equipos.eliminar')
                            <form action="{{ route('equipos.destroy', $equipo->id_equipo) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de eliminar este equipo?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm" title="Eliminar">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @endpuede
                        </td>
                        @endpuede
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">No hay equipos registrados actualmente.</td>
                    </tr>
                    @endforelse

                    <!-- Fila oculta por defecto para cuando no hay coincidencias en la búsqueda -->
                    <tr id="noResultsRow" style="display: none;">
                        <td colspan="9" class="text-center text-muted py-4">
                            <i class="bi bi-search me-2"></i>No se encontraron equipos coincidentes con la búsqueda.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
