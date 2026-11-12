@extends($imprimir ? 'Reporte.print-layout' : 'layouts.app')

@section('title', 'Reporte de Mantenimientos - Control de Activos')

@section('content')

@unless($imprimir)
    @include('Reporte._cabecera', ['icono' => 'bi-tools', 'ruta' => 'reportes.mantenimientos'])

    <!-- Filtros -->
    <form method="GET" action="{{ route('reportes.mantenimientos') }}" class="card mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label for="codigo" class="form-label small fw-semibold mb-1">Código contable</label>
                    <input type="text" id="codigo" name="codigo" class="form-control" value="{{ $f['codigo'] }}">
                </div>
                <div class="col-md-2">
                    <label for="desde" class="form-label small fw-semibold mb-1">Fecha desde</label>
                    <input type="date" id="desde" name="desde" class="form-control" value="{{ $f['desde'] }}">
                </div>
                <div class="col-md-2">
                    <label for="hasta" class="form-label small fw-semibold mb-1">Fecha hasta</label>
                    <input type="date" id="hasta" name="hasta" class="form-control" value="{{ $f['hasta'] }}">
                </div>
                <div class="col-md-2">
                    <label for="mes" class="form-label small fw-semibold mb-1">Mes</label>
                    <input type="month" id="mes" name="mes" class="form-control" value="{{ $f['mes'] }}">
                </div>
                <div class="col-md-2">
                    <label for="tecnico" class="form-label small fw-semibold mb-1">Técnico</label>
                    <select id="tecnico" name="tecnico" class="form-select">
                        <option value="">Todos</option>
                        @foreach($tecnicos as $tecnico)
                            <option value="{{ $tecnico->id_empleado }}" @selected($f['tecnico'] == $tecnico->id_empleado)>{{ $tecnico->nombre_empleado }} {{ $tecnico->apellido_empleado }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-search me-1"></i> Buscar</button>
                    <a href="{{ route('reportes.mantenimientos') }}" class="btn btn-outline-secondary btn-sm">Limpiar</a>
                </div>
            </div>
        </div>
    </form>
@endunless

@include('Reporte._kpis', ['kpis' => [
    ['num' => $mantenimientos->count(), 'etq' => 'Mantenimientos realizados (según filtros)', 'color' => 'primary'],
    ['num' => $totalGeneral, 'etq' => 'Total de mantenimientos registrados', 'color' => 'secondary'],
]])

<div class="{{ $imprimir ? 'resumenes' : 'row g-3 mb-4' }}">
    <div class="{{ $imprimir ? '' : 'col-md-6' }}">
        @include('Reporte._resumen', ['titulo' => 'Mantenimientos por mes', 'columna' => 'Mes', 'filas' => $porMes])
    </div>
    <div class="{{ $imprimir ? '' : 'col-md-6' }}">
        @include('Reporte._resumen', ['titulo' => 'Mantenimientos por técnico', 'columna' => 'Técnico', 'filas' => $porTecnico])
    </div>
</div>

@if($imprimir)
    <div class="seccion-titulo">Listado de mantenimientos</div>
@else
    <h2 class="h6 fw-bold text-uppercase text-secondary mb-2">Listado de mantenimientos</h2>
@endif

<div class="{{ $imprimir ? '' : 'card' }}">
    <div class="{{ $imprimir ? '' : 'table-responsive' }}">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-dark">
                <tr>
                    <th>Fecha</th>
                    <th>Código Contable</th>
                    <th>Empleado</th>
                    <th>Afiliado</th>
                    <th>Descripción</th>
                    <th>Técnico</th>
                </tr>
            </thead>
            <tbody>
                @forelse($mantenimientos as $m)
                    <tr>
                        <td>{{ $m->fecha_mantenimiento ? \Carbon\Carbon::parse($m->fecha_mantenimiento)->format('d/m/Y') : 'N/A' }}</td>
                        <td class="fw-semibold">{{ $m->equipo->codigo_contable ?? 'N/A' }}</td>
                        <td>{{ trim(($m->empleado->nombre_empleado ?? '') . ' ' . ($m->empleado->apellido_empleado ?? '')) ?: 'N/A' }}</td>
                        <td>{{ $m->empleado->afiliado->nombre_afiliado ?? 'N/A' }}</td>
                        <td>{{ $imprimir ? $m->descripcion : \Illuminate\Support\Str::limit($m->descripcion, 60) }}</td>
                        <td>{{ $m->tecnico ? $m->tecnico->nombre_empleado . ' ' . $m->tecnico->apellido_empleado : 'Sin técnico registrado' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No hay mantenimientos con esos filtros.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
