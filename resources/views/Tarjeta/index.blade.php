@extends('layouts.app')

@section('title', 'Tarjeta de Responsabilidad - Control de Activos')

@section('content')

@vite('resources/js/buscar-tarjeta.js')

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2 fw-bold text-dark"><i class="bi bi-person-vcard me-2"></i>Tarjeta de Responsabilidad</h1>
</div>

<p class="text-muted">Busque por el código contable de un equipo o por el nombre del empleado para ver qué equipos tiene actualmente bajo su responsabilidad.</p>

<!-- Buscador interactivo -->
<form onsubmit="return false;" style="margin-bottom: 1.5rem; display: flex; gap: 0.5rem; flex-wrap: wrap;">
    <input type="text" id="searchInput" placeholder="Buscar por código contable del equipo (completo o últimos dígitos) o nombre del empleado..."
           style="flex: 1; min-width: 250px; padding: 0.5rem 0.75rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.875rem;">
</form>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th class="py-3 ps-3">Código L4</th>
                        <th class="py-3">Empleado</th>
                        <th class="py-3">Puesto</th>
                        <th class="py-3">Afiliado</th>
                        <th class="py-3">Estado</th>
                        <th class="py-3">Equipos bajo su responsabilidad</th>
                        <th class="py-3 text-end pe-3">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($filas as $fila)
                        @php
                            $empleado = $fila['empleado'];
                            $nombre = trim($empleado->nombre_empleado . ' ' . $empleado->apellido_empleado);
                            $codigos = $fila['aCargo']->map(fn ($t) => \Illuminate\Support\Str::lower($t->equipo->codigo_contable))->implode(' ');
                        @endphp
                        <tr class="fila-tarjeta"
                            data-nombre="{{ \Illuminate\Support\Str::lower($nombre) }}"
                            data-l4="{{ \Illuminate\Support\Str::lower($empleado->cod_l4 ?? '') }}"
                            data-equipos="{{ $codigos }}">
                            <td class="ps-3 fw-semibold">{{ $empleado->cod_l4 ?? 'N/A' }}</td>
                            <td class="fw-semibold">{{ $nombre }}</td>
                            <td>{{ $empleado->puesto->nombre_puesto ?? 'N/A' }}</td>
                            <td>{{ $empleado->afiliado->nombre_afiliado ?? 'N/A' }}</td>
                            <td>
                                <span class="badge {{ $empleado->estado?->claseBadge() ?? 'bg-secondary' }}">{{ $empleado->estado->nombre_estado ?? 'N/A' }}</span>
                            </td>
                            <td>
                                @forelse($fila['aCargo'] as $traslado)
                                    <div>
                                        <span class="badge bg-primary">{{ $traslado->equipo->codigo_contable }}</span>
                                        <span class="small text-muted ms-1">
                                            {{ $traslado->equipo->tipoEquipo->nombre_tipo ?? '' }}
                                            {{ $traslado->equipo->marca->nombre_marca ?? '' }}
                                            {{ $traslado->equipo->modelo->nombre_modelo ?? '' }}
                                        </span>
                                    </div>
                                @empty
                                    <span class="text-muted">Sin equipos</span>
                                @endforelse

                                @if($fila['sinDevolver']->isNotEmpty())
                                    <div class="small text-warning-emphasis mt-1" title="El empleado fue dado de baja y estos equipos no se han devuelto en Traslados">
                                        <i class="bi bi-exclamation-triangle-fill me-1"></i>Sin devolver:
                                        {{ $fila['sinDevolver']->map(fn ($t) => $t->equipo->codigo_contable)->implode(', ') }}
                                    </div>
                                @endif
                            </td>
                            <td class="text-end pe-3">
                                <a href="{{ route('tarjetas.show', $empleado->id_empleado) }}" class="btn btn-outline-secondary btn-sm" title="Ver tarjeta">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('tarjetas.imprimir', $empleado->id_empleado) }}" class="btn btn-outline-success btn-sm" title="Imprimir tarjeta" target="_blank">
                                    <i class="bi bi-printer"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No hay empleados registrados.</td>
                        </tr>
                    @endforelse

                    <!-- Fila mostrada cuando el filtro no encuentra coincidencias -->
                    <tr id="filaSinResultados" style="display: none;">
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="bi bi-search me-2"></i>Ningún empleado coincide con la búsqueda.
                            <div class="small mt-1">Si busca un equipo, puede estar disponible o dado de baja: ningún empleado lo tiene bajo su responsabilidad.</div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
