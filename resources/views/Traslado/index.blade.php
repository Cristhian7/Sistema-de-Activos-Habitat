@extends('layouts.app')

@section('title', 'Listado de Traslados - Control de Activos')

@section('content')

@vite('resources/js/buscar-traslado.js')

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2 fw-bold text-dark"><i class="bi bi-arrow-left-right me-2"></i>Listado de Traslados</h1>
    @puede('traslados.crear')
    <a href="{{ route('traslados.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i> Nuevo Traslado
    </a>
    @endpuede
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- Aviso: traslados a los que aún les falta subir el documento firmado --}}
@php
    $pendientes = $traslados->where('tiene_documento', false)->sortBy('id_traslado')->map(fn ($t) => [
        'texto' => ($t->equipo->codigo_contable ?? 'N/A') . ' — '
            . trim(($t->empleado->nombre_empleado ?? '') . ' ' . ($t->empleado->apellido_empleado ?? ''))
            . ' — traslado del ' . ($t->fecha_envio ? \Carbon\Carbon::parse($t->fecha_envio)->format('d/m/Y') : 'N/A'),
        'url' => route('traslados.show', $t->id_traslado),
    ])->values();
@endphp
@include('partials.alerta-documentos', ['pendientes' => $pendientes, 'singular' => 'traslado', 'plural' => 'traslados', 'id' => 'pendientesTraslados', 'permiso' => 'traslados'])

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
                        <th class="py-3">Situación</th>
                        <th class="py-3">Técnico</th>
                        <th class="py-3">Documento firmado</th>
                        <th class="py-3 text-end pe-3">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($traslados as $traslado)
                    @php
                        $nombreEmpleado = trim(($traslado->empleado->nombre_empleado ?? '') . ' ' . ($traslado->empleado->apellido_empleado ?? ''));
                        $equipoEnBaja = \App\Models\Estado\Estado::esBaja($traslado->equipo->estado_id_estado ?? null);
                        $empleadoEnBaja = \App\Models\Estado\Estado::esBaja($traslado->empleado->estado_id_estado ?? null);
                        $bajaDelEquipo = $bajasPorEquipo->get($traslado->equipo_id_equipo);
                        // Se puede devolver mientras el traslado está vigente y el equipo no esté dado de baja
                        $sePuedeDevolver = $traslado->vigente && !$equipoEnBaja;
                    @endphp
                    <tr class="fila-traslado"
                        data-codigo="{{ Str::lower($traslado->equipo->codigo_contable ?? '') }}"
                        data-empleado="{{ Str::lower($nombreEmpleado) }}"
                        data-st="{{ Str::lower($traslado->equipo->st ?? '') }}">
                        <!--<td class="ps-3 fw-semibold">{{ $traslado->id_traslado }}</td>-->
                        <td class="codigo-equipo ps-3 fw-semibold">{{ $traslado->equipo->codigo_contable ?? 'N/A' }} </td>
                        <td>{{ $traslado->equipo->tipoEquipo->nombre_tipo ?? 'N/A' }}</td>
                        <td>{{ $traslado->equipo->marca->nombre_marca ?? 'N/A' }}</td>
                        <td>{{ $traslado->equipo->modelo->nombre_modelo ?? 'N/A' }}</td>
                        <td class="nombre-empleado ps-3 fw-semibold">
                            {{ $traslado->empleado->nombre_empleado ?? 'N/A' }}
                            @if($empleadoEnBaja)
                                <span class="badge bg-danger ms-1" title="El empleado está dado de baja">Baja</span>
                            @endif
                        </td>
                        <td class="nombre-empleado ps-3 fw-semibold">{{ $traslado->empleado->apellido_empleado ?? 'N/A' }} </td>
                        <td>{{ $traslado->empleado->puesto->nombre_puesto ?? 'N/A' }}</td>
                        <td>{{ $traslado->empleado->afiliado->nombre_afiliado ?? 'N/A' }}</td>
                        <td>{{ $traslado->fecha_envio }}</td>
                        <td>
                            {{-- Qué pasó con este traslado: se dice claramente si la baja fue del EQUIPO o del EMPLEADO --}}
                            @if(!$traslado->vigente)
                                @switch($traslado->causa_devolucion)
                                    @case(\App\Models\Traslado\Traslado::CAUSA_BAJA_EMPLEADO)
                                        <span class="badge bg-dark"><i class="bi bi-person-x me-1"></i>Empleado dado de baja</span>
                                        @break
                                    @case(\App\Models\Traslado\Traslado::CAUSA_REASIGNACION)
                                        <span class="badge bg-info text-dark"><i class="bi bi-arrow-repeat me-1"></i>Reasignado</span>
                                        @break
                                    @default
                                        <span class="badge bg-secondary"><i class="bi bi-box-arrow-in-left me-1"></i>Devuelto</span>
                                @endswitch
                                <div class="small text-muted mt-1">
                                    {{ \Carbon\Carbon::parse($traslado->fecha_devolucion)->format('d/m/Y') }}
                                    @if($traslado->causa_devolucion !== \App\Models\Traslado\Traslado::CAUSA_BAJA_EMPLEADO && $traslado->motivo_devolucion)
                                        — {{ \Illuminate\Support\Str::limit($traslado->motivo_devolucion, 40) }}
                                    @endif
                                </div>
                            @elseif(!$equipoEnBaja && !$empleadoEnBaja)
                                <span class="badge bg-success">Vigente</span>
                            @endif

                            @if($equipoEnBaja)
                                <div class="{{ $traslado->vigente ? '' : 'mt-1' }}">
                                    <span class="badge bg-danger"><i class="bi bi-laptop me-1"></i>Equipo dado de baja</span>
                                    @if($bajaDelEquipo?->fecha_baja)
                                        <div class="small text-muted mt-1">{{ \Carbon\Carbon::parse($bajaDelEquipo->fecha_baja)->format('d/m/Y') }}</div>
                                    @endif
                                </div>
                            @endif

                            @if($traslado->vigente && $empleadoEnBaja)
                                <div class="{{ $equipoEnBaja ? 'mt-1' : '' }}">
                                    <span class="badge bg-warning text-dark" title="El empleado fue dado de baja y el equipo no se había devuelto">
                                        <i class="bi bi-person-x me-1"></i>Empleado dado de baja
                                    </span>
                                    <div class="small text-muted mt-1">Equipo sin devolver</div>
                                </div>
                            @endif
                        </td>
                        <td class="fw-semibold">
                            @if($traslado->tecnico)
                                {{ $traslado->tecnico->nombre_empleado }} {{ $traslado->tecnico->apellido_empleado }}
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td>
                            @if($traslado->tiene_documento)
                                <a href="{{ route('traslados.documento.ver', $traslado->id_traslado) }}" target="_blank" class="badge bg-success text-decoration-none" title="Ver el documento firmado">
                                    <i class="bi bi-file-earmark-check me-1"></i>Cargado
                                </a>
                            @else
                                <a href="{{ route('traslados.show', $traslado->id_traslado) }}#documento-firmado" class="badge bg-warning text-dark text-decoration-none" title="Falta subir el documento firmado">
                                    <i class="bi bi-exclamation-triangle me-1"></i>Pendiente
                                </a>
                            @endif
                        </td>

                        <td class="text-end pe-3">
                            <a href="{{ route('traslados.show', $traslado->id_traslado) }}" class="btn btn-outline-secondary btn-sm" title="Ver">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('traslados.imprimir', $traslado->id_traslado) }}" class="btn btn-outline-success btn-sm" title="Imprimir" target="_blank">
                                <i class="bi bi-printer"></i>
                            </a>
                            @if($sePuedeDevolver && \App\Support\Permisos::puede('traslados.editar'))
                                <button type="button" class="btn btn-outline-warning btn-sm btn-devolver" title="Devolver equipo (quitarlo al empleado)"
                                        data-bs-toggle="modal" data-bs-target="#modalDevolver"
                                        data-action="{{ route('traslados.devolver', $traslado->id_traslado) }}"
                                        data-equipo="{{ $traslado->equipo->codigo_contable ?? '' }}"
                                        data-empleado="{{ $nombreEmpleado }}"
                                        data-motivo="{{ $empleadoEnBaja ? 'Empleado dado de baja' : '' }}"
                                        data-min="{{ \Carbon\Carbon::parse($traslado->fecha_envio)->format('Y-m-d') }}">
                                    <i class="bi bi-box-arrow-in-left"></i>
                                </button>
                            @endif
                            @if($traslado->vigente)
                                @puede('traslados.editar')
                                <a href="{{ route('traslados.edit', $traslado->id_traslado) }}" class="btn btn-outline-primary btn-sm" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @endpuede
                            @endif
                            @puede('traslados.eliminar')
                            <form action="{{ route('traslados.destroy', $traslado->id_traslado) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de eliminar este traslado?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm" title="Eliminar">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @endpuede
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="13" class="text-center text-muted py-4">No hay traslados registrados actualmente.</td>
                    </tr>
                    @endforelse
                    <!-- Fila mostrada cuando el filtro no encuentra coincidencias -->
                    <tr id="filaSinResultados" style="display: none;">
                        <td colspan="13" class="text-center text-muted py-4">No se encontraron traslados que coincidan con la búsqueda.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Ventana para devolver un equipo (quitárselo al empleado) -->
<div class="modal fade" id="modalDevolver" tabindex="-1" aria-labelledby="modalDevolverLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" id="formDevolver" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="modalDevolverLabel"><i class="bi bi-box-arrow-in-left me-2"></i>Devolver equipo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <p class="mb-3">
                    Se le quitará el equipo <strong id="devolverEquipo"></strong> a <strong id="devolverEmpleado"></strong>.
                    El equipo quedará <strong>Disponible</strong> para asignarlo a otro empleado y el movimiento quedará en la bitácora.
                </p>
                <div class="mb-3">
                    <label for="fecha_devolucion" class="form-label fw-semibold">Fecha de devolución <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="fecha_devolucion" name="fecha_devolucion" max="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="mb-0">
                    <label for="motivo_devolucion" class="form-label fw-semibold">Motivo de la devolución <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="motivo_devolucion" name="motivo_devolucion" maxlength="255" placeholder="Ej. Cambio de equipo, renuncia, falla..." required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-warning fw-semibold"><i class="bi bi-check-lg me-1"></i>Confirmar devolución</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Prepara la ventana de devolución con los datos del traslado elegido
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('modalDevolver');
        if (!modal) return;

        modal.addEventListener('show.bs.modal', function (evento) {
            const boton = evento.relatedTarget;
            if (!boton) return;

            document.getElementById('formDevolver').action = boton.dataset.action;
            document.getElementById('devolverEquipo').textContent = boton.dataset.equipo;
            document.getElementById('devolverEmpleado').textContent = boton.dataset.empleado;
            document.getElementById('fecha_devolucion').min = boton.dataset.min;
            // Si el empleado ya fue dado de baja, se sugiere ese motivo
            document.getElementById('motivo_devolucion').value = boton.dataset.motivo || '';
        });
    });
</script>

@endsection
