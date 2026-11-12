@extends($imprimir ? 'Reporte.print-layout' : 'layouts.app')

@section('title', 'Equipos por Reemplazar - Control de Activos')

@section('content')

@unless($imprimir)
    @include('Reporte._cabecera', ['icono' => 'bi-arrow-repeat', 'ruta' => 'reportes.reemplazo'])

    <div class="alert alert-info py-2 small">
        <i class="bi bi-info-circle me-1"></i>
        Regla: un equipo se puede reemplazar cuando cumple <strong>{{ $anos }} años</strong> desde su fecha de compra. No se incluyen los equipos dados de baja.
    </div>

    <!-- Filtros -->
    <form method="GET" action="{{ route('reportes.reemplazo') }}" class="card mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="codigo" class="form-label small fw-semibold mb-1">Código contable</label>
                    <input type="text" id="codigo" name="codigo" class="form-control" value="{{ $f['codigo'] }}">
                </div>
                <div class="col-md-3">
                    <label for="empleado" class="form-label small fw-semibold mb-1">Nombre del empleado</label>
                    <input type="text" id="empleado" name="empleado" class="form-control" value="{{ $f['empleado'] }}">
                </div>
                <div class="col-md-3">
                    <label for="afiliado" class="form-label small fw-semibold mb-1">Afiliado</label>
                    <select id="afiliado" name="afiliado" class="form-select">
                        <option value="">Todos</option>
                        @foreach($afiliados as $afiliado)
                            <option value="{{ $afiliado->id_afiliado }}" @selected($f['afiliado'] == $afiliado->id_afiliado)>{{ $afiliado->nombre_afiliado }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-search me-1"></i> Buscar</button>
                    <a href="{{ route('reportes.reemplazo') }}" class="btn btn-outline-secondary btn-sm">Limpiar</a>
                </div>
            </div>
        </div>
    </form>
@endunless

@include('Reporte._kpis', ['kpis' => [
    ['num' => $filas->count(), 'etq' => 'Equipos que ya se pueden reemplazar (según filtros)', 'color' => 'warning'],
    ['num' => $totalReemplazables, 'etq' => 'Total reemplazables sin filtros', 'color' => 'secondary'],
    ['num' => $asignados, 'etq' => 'Asignados a un empleado', 'color' => 'primary'],
    ['num' => $disponibles, 'etq' => 'Disponibles (sin asignar)', 'color' => 'success'],
]])

@if($imprimir)
    <div class="seccion-titulo">Listado de equipos por reemplazar</div>
@else
    <h2 class="h6 fw-bold text-uppercase text-secondary mb-2">Listado de equipos por reemplazar</h2>
@endif

<div class="{{ $imprimir ? '' : 'card' }}">
    <div class="{{ $imprimir ? '' : 'table-responsive' }}">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-dark">
                <tr>
                    <th>No.</th>
                    <th>Código Contable</th>
                    <th>Equipo</th>
                    <th>Marca / Modelo</th>
                    <th>Fecha de compra</th>
                    <th>Antigüedad</th>
                    <th>Empleado</th>
                    <th>Afiliado</th>
                </tr>
            </thead>
            <tbody>
                @forelse($filas as $i => $fila)
                    @php $equipo = $fila['equipo']; @endphp
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td class="fw-semibold">{{ $equipo->codigo_contable }}</td>
                        <td>{{ $equipo->tipoEquipo->nombre_tipo ?? 'N/A' }}</td>
                        <td>{{ $equipo->marca->nombre_marca ?? 'N/A' }} {{ $equipo->modelo->nombre_modelo ?? '' }}</td>
                        <td>{{ \Carbon\Carbon::parse($equipo->fecha_compra)->format('d/m/Y') }}</td>
                        <td>{{ $fila['antiguedad'] }}</td>
                        <td>{{ $fila['nombre'] ?? 'Disponible (sin asignar)' }}</td>
                        <td>{{ $fila['afiliado']->nombre_afiliado ?? 'N/A' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">No hay equipos por reemplazar con esos filtros.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
