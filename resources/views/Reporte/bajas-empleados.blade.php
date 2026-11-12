@extends($imprimir ? 'Reporte.print-layout' : 'layouts.app')

@section('title', 'Reporte de Bajas de Empleados - Control de Activos')

@section('content')

@unless($imprimir)
    @include('Reporte._cabecera', ['icono' => 'bi-person-x', 'ruta' => 'reportes.bajas-empleados'])

    <!-- Filtros -->
    <form method="GET" action="{{ route('reportes.bajas-empleados') }}" class="card mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="afiliado" class="form-label small fw-semibold mb-1">Afiliado</label>
                    <select id="afiliado" name="afiliado" class="form-select">
                        <option value="">Todos</option>
                        @foreach($afiliados as $afiliado)
                            <option value="{{ $afiliado->id_afiliado }}" @selected($f['afiliado'] == $afiliado->id_afiliado)>{{ $afiliado->nombre_afiliado }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="desde" class="form-label small fw-semibold mb-1">Fecha de baja desde</label>
                    <input type="date" id="desde" name="desde" class="form-control" value="{{ $f['desde'] }}">
                </div>
                <div class="col-md-3">
                    <label for="hasta" class="form-label small fw-semibold mb-1">Fecha de baja hasta</label>
                    <input type="date" id="hasta" name="hasta" class="form-control" value="{{ $f['hasta'] }}">
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-search me-1"></i> Buscar</button>
                    <a href="{{ route('reportes.bajas-empleados') }}" class="btn btn-outline-secondary btn-sm">Limpiar</a>
                </div>
            </div>
        </div>
    </form>
@endunless

@include('Reporte._kpis', ['kpis' => [
    ['num' => $empleados->count(), 'etq' => 'Empleados dados de baja (según filtros)', 'color' => 'danger'],
    ['num' => $totalGeneral, 'etq' => 'Total de empleados en baja', 'color' => 'secondary'],
]])

<div class="{{ $imprimir ? 'resumenes' : 'row g-3 mb-4' }}">
    <div class="{{ $imprimir ? '' : 'col-md-6' }}">
        @include('Reporte._resumen', ['titulo' => 'Bajas por afiliado', 'columna' => 'Afiliado', 'filas' => $porAfiliado])
    </div>
    <div class="{{ $imprimir ? '' : 'col-md-6' }}">
        @include('Reporte._resumen', ['titulo' => 'Bajas por mes', 'columna' => 'Mes', 'filas' => $porMes])
    </div>
</div>

@if($sinFecha > 0)
    <div class="{{ $imprimir ? 'text-muted' : 'alert alert-secondary py-2 small' }}" style="{{ $imprimir ? 'margin-bottom:8px;' : '' }}">
        {{ $sinFecha }} {{ $sinFecha === 1 ? 'empleado fue dado de baja' : 'empleados fueron dados de baja' }} antes de que el sistema guardara la fecha y no tienen fecha registrada.
        @unless($f['desde'] || $f['hasta']) Aparecen en el listado, pero no se pueden contar por mes. @else No se incluyen cuando se filtra por fecha. @endunless
    </div>
@endif

@if($imprimir)
    <div class="seccion-titulo">Listado de empleados dados de baja</div>
@else
    <h2 class="h6 fw-bold text-uppercase text-secondary mb-2">Listado de empleados dados de baja</h2>
@endif

<div class="{{ $imprimir ? '' : 'card' }}">
    <div class="{{ $imprimir ? '' : 'table-responsive' }}">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-dark">
                <tr>
                    <th>Empleado</th>
                    <th>Código L4</th>
                    <th>Puesto</th>
                    <th>Afiliado</th>
                    <th>Fecha de baja</th>
                </tr>
            </thead>
            <tbody>
                @forelse($empleados as $empleado)
                    <tr>
                        <td class="fw-semibold">{{ $empleado->nombre_empleado }} {{ $empleado->apellido_empleado }}</td>
                        <td>{{ $empleado->cod_l4 ?? 'N/A' }}</td>
                        <td>{{ $empleado->puesto->nombre_puesto ?? 'N/A' }}</td>
                        <td>{{ $empleado->afiliado->nombre_afiliado ?? 'N/A' }}</td>
                        <td>{{ $empleado->fecha_baja ? \Carbon\Carbon::parse($empleado->fecha_baja)->format('d/m/Y') : 'Sin fecha registrada' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">No hay empleados dados de baja con esos filtros.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
