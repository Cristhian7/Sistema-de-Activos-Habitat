@extends('layouts.app')

@section('title', 'Listado de Traslados - Control de Activos')

@section('content')

@vite('resources/js/buscar-traslado.js')

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

<!-- Barra de Búsqueda Interactiva -->
<form action="{{ route('traslados.index') }}" method="GET" style="margin-bottom: 1.5rem; display: flex; gap: 0.5rem; flex-wrap: wrap;" onsubmit="return false;">
    <input type="text" id="searchInput" name="search" placeholder="Buscar por código contable, empleado y service tag" value="{{ request('search') }}"
           style="flex: 1; min-width: 250px; padding: 0.5rem 0.75rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.875rem;">
</form>

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
                    <tr class="fila-traslado"
                        data-codigo="{{ Str::lower($traslado->equipo->codigo_contable ?? '') }}"
                        data-empleado="{{ Str::lower(trim(($traslado->empleado->nombre_empleado ?? '') . ' ' . ($traslado->empleado->apellido_empleado ?? ''))) }}"
                        data-st="{{ Str::lower($traslado->equipo->st ?? '') }}">
                        <!--<td class="ps-3 fw-semibold">{{ $traslado->id_traslado }}</td>-->
                        <td class="codigo-equipo ps-3 fw-semibold">{{ $traslado->equipo->codigo_contable ?? 'N/A' }} </td>
                        <td>{{ $traslado->equipo->tipoEquipo->nombre_tipo ?? 'N/A' }}</td>
                        <td>{{ $traslado->equipo->marca->nombre_marca ?? 'N/A' }}</td>
                        <td>{{ $traslado->equipo->modelo->nombre_modelo ?? 'N/A' }}</td>
                        <td class="nombre-empleado ps-3 fw-semibold">{{ $traslado->empleado->nombre_empleado ?? 'N/A' }} </td>
                        <td class="nombre-empleado ps-3 fw-semibold">{{ $traslado->empleado->apellido_empleado ?? 'N/A' }} </td>
                        <td>{{ $traslado->empleado->puesto->nombre_puesto ?? 'N/A' }}</td>
                        <td>{{ $traslado->empleado->afiliado->nombre_afiliado ?? 'N/A' }}</td>
                        <td>{{ $traslado->fecha_envio }}</td>

                        <td class="text-end pe-3">
                            <a href="{{ route('traslados.show', $traslado->id_traslado) }}" class="btn btn-outline-secondary btn-sm" title="Ver">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('traslados.imprimir', $traslado->id_traslado) }}" class="btn btn-outline-success btn-sm" title="Imprimir" target="_blank">
                                <i class="bi bi-printer"></i>
                            </a>
                            <a href="{{ route('traslados.edit', $traslado->id_traslado) }}" class="btn btn-outline-primary btn-sm" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('traslados.destroy', $traslado->id_traslado) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de eliminar este traslado?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm" title="Eliminar">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center text-muted py-4">No hay traslados registrados actualmente.</td>
                    </tr>
                    @endforelse
                    <!-- Fila mostrada cuando el filtro no encuentra coincidencias -->
                    <tr id="filaSinResultados" style="display: none;">
                        <td colspan="10" class="text-center text-muted py-4">No se encontraron traslados que coincidan con la búsqueda.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection