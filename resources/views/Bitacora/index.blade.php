@extends('layouts.app')

@section('title', 'Bitácora de Equipos - Control de Activos')

@section('content')

@vite('resources/js/buscar-bitacora.js')

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2 fw-bold text-dark"><i class="bi bi-journal-text me-2"></i>Bitácora de Equipos</h1>
</div>

<p class="text-muted">Historial de movimientos de cada equipo (traslados y bajas): quién lo tiene actualmente o quién fue el último en usarlo.</p>

<!-- Buscador interactivo -->
<form onsubmit="return false;" style="margin-bottom: 1.5rem; display: flex; gap: 0.5rem; flex-wrap: wrap;">
    <input type="text" id="searchInput" placeholder="Buscar por código contable (completo o últimos dígitos), nombre del equipo o empleado..."
           style="flex: 1; min-width: 250px; padding: 0.5rem 0.75rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.875rem;">
</form>

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
                        <th class="py-3">Estado</th>
                        <th class="py-3">Situación Actual</th>
                        <th class="py-3">Último Movimiento</th>
                        <th class="py-3 text-center">Movimientos</th>
                        <th class="py-3 text-end pe-3">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($filas as $fila)
                        @php
                            $equipo = $fila['equipo'];
                            $sit = $fila['situacion'];
                            $ultimo = $fila['movimientos']->first();
                            $nombreEmpleado = $sit['empleado']
                                ? trim($sit['empleado']->nombre_empleado . ' ' . $sit['empleado']->apellido_empleado)
                                : '';
                        @endphp
                        <tr class="fila-bitacora"
                            data-codigo="{{ Str::lower($equipo->codigo_contable) }}"
                            data-nombre="{{ Str::lower($equipo->nombre_equipo) }}"
                            data-empleado="{{ Str::lower($nombreEmpleado) }}">
                            <td class="ps-3 fw-semibold">{{ $equipo->codigo_contable }}</td>
                            <td>{{ $equipo->nombre_equipo }}</td>
                            <td>{{ $equipo->tipoEquipo->nombre_tipo ?? 'N/A' }}</td>
                            <td>{{ $equipo->marca->nombre_marca ?? 'N/A' }}</td>
                            <td>{{ $equipo->modelo->nombre_modelo ?? 'N/A' }}</td>
                            <td>
                                <span class="badge {{ $equipo->estado?->claseBadge() ?? 'bg-secondary' }}">{{ $equipo->estado->nombre_estado ?? 'N/A' }}</span>
                            </td>
                            <td>
                                @if($sit['tipo'] === 'sin_asignar')
                                    <span class="badge bg-success">Disponible</span>
                                    <div class="small text-muted mt-1">Nunca asignado</div>
                                @else
                                    @if($sit['tipo'] === 'disponible')
                                        <span class="badge bg-success mb-1">Disponible</span>
                                    @endif
                                    <div class="small text-muted">{{ $sit['titulo'] }}</div>
                                    <div class="fw-semibold">{{ $nombreEmpleado ?: 'N/A' }}</div>
                                    <div class="small text-muted">{{ $sit['empleado']->afiliado->nombre_afiliado ?? '' }}</div>
                                @endif
                            </td>
                            <td>
                                @if($ultimo)
                                    <span class="badge {{ $ultimo['badge'] }}">{{ $ultimo['titulo'] }}</span>
                                    <div class="small text-muted mt-1">{{ $ultimo['fecha'] ? $ultimo['fecha']->format('d/m/Y') : 'N/A' }}</div>
                                @else
                                    <span class="text-muted">Sin movimientos</span>
                                @endif
                            </td>
                            <td class="text-center">{{ $fila['movimientos']->count() }}</td>
                            <td class="text-end pe-3">
                                <a href="{{ route('bitacora.show', $equipo->id_equipo) }}" class="btn btn-outline-secondary btn-sm" title="Ver historial">
                                    <i class="bi bi-clock-history"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">No hay equipos registrados.</td>
                        </tr>
                    @endforelse

                    <!-- Fila mostrada cuando el filtro no encuentra coincidencias -->
                    <tr id="filaSinResultados" style="display: none;">
                        <td colspan="10" class="text-center text-muted py-4">
                            <i class="bi bi-search me-2"></i>No se encontraron equipos que coincidan con la búsqueda.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
