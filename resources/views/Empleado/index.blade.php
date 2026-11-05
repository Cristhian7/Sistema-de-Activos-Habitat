@extends('layouts.app')

@section('title', 'Listado de Empleados - Control de Activos')

@section('content')

<!-- Cargando el script de búsqueda mediante Vite -->
@vite(['resources/js/buscar-empleado.js'])

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2 fw-bold text-dark"><i class="bi bi-people me-2"></i>Listado de Empleados</h1>
    <a href="{{ route('empleados.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i> Nuevo Empleado
    </a>
</div>

<!-- Alerta de éxito si existe -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Barra de Búsqueda Interactiva (Apunta a empleados.index) -->
<form action="{{ route('empleados.index') }}" method="GET" style="margin-bottom: 1.5rem; display: flex; gap: 0.5rem; flex-wrap: wrap;" onsubmit="return false;">
    <input type="text" id="searchInput" name="search" placeholder="Buscar por nombre, apellido o código L4..." value="{{ request('search') }}"
           style="flex: 1; min-width: 250px; padding: 0.5rem 0.75rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.875rem;">
</form>

<!-- Tabla de Empleados -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <!--<th class="py-3 ps-3">ID</th>-->
                        <th class="py-3">Nombre</th>
                        <th class="py-3">Apellido</th>
                        <th class="py-3">Código L4</th>
                        <th class="py-3">Puesto</th>
                        <th class="py-3">Afiliado</th>
                        <th class="py-3">Rol</th>
                        <th class="py-3">Estado</th>
                        <th class="py-3 text-end pe-3">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($empleados as $empleado)
                    <!-- Fila con la clase requerida para el buscador JS -->
                    <tr class="empleado-row">
                        <!--<td class="ps-3 fw-semibold">{{ $empleado->id_empleado }}</td> -->
                        <td class="nombre-empleado text-dark fw-medium">{{ $empleado->nombre_empleado }}</td>
                        <td class="apellido-empleado">{{ $empleado->apellido_empleado }}</td>
                        <td class="cod-l4">
                            <span class="badge bg-light text-dark border">{{ $empleado->cod_l4 ?? 'N/A' }}</span>
                        </td>
                        <td>{{ $empleado->puesto->nombre_puesto ?? 'Sin puesto' }}</td>
                        <td>{{ $empleado->afiliado->nombre_afiliado ?? 'Sin afiliado' }}</td>
                        <td>{{ $empleado->rol->nombre_rol ?? 'Sin rol' }}</td>
                        <td>
                            @if($empleado->estado_id_estado == 1)
                                <span class="badge bg-success">Activo</span>
                            @else
                                <span class="badge bg-danger">Baja</span>
                            @endif
                        </td>
                        <td class="text-end pe-3">
                            <a href="{{ route('empleados.edit', $empleado->id_empleado) }}" class="btn btn-outline-primary btn-sm" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('empleados.destroy', $empleado->id_empleado) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de dar de baja a este empleado?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm" title="Dar de baja">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">No hay empleados registrados actualmente.</td>
                    </tr>
                    @endforelse

                    <!-- Fila oculta por defecto para cuando no hay coincidencias en la búsqueda -->
                    <tr id="noResultsRow" style="display: none;">
                        <td colspan="9" class="text-center text-muted py-4">
                            <i class="bi bi-search me-2"></i>No se encontraron empleados coincidentes con la búsqueda.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection