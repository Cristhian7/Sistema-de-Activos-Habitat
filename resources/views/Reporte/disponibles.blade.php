@extends($imprimir ? 'Reporte.print-layout' : 'layouts.app')

@section('title', 'Equipos Disponibles - Control de Activos')

@section('content')

@unless($imprimir)
    @include('Reporte._cabecera', ['icono' => 'bi-box-seam', 'ruta' => 'reportes.disponibles'])

    <!-- Filtros -->
    <form method="GET" action="{{ route('reportes.disponibles') }}" class="card mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="codigo" class="form-label small fw-semibold mb-1">Código contable</label>
                    <input type="text" id="codigo" name="codigo" class="form-control" value="{{ $f['codigo'] }}">
                </div>
                <div class="col-md-4">
                    <label for="st" class="form-label small fw-semibold mb-1">ST</label>
                    <input type="text" id="st" name="st" class="form-control" value="{{ $f['st'] }}">
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-search me-1"></i> Buscar</button>
                    <a href="{{ route('reportes.disponibles') }}" class="btn btn-outline-secondary btn-sm">Limpiar</a>
                </div>
            </div>
        </div>
    </form>
@endunless

@include('Reporte._kpis', ['kpis' => [
    ['num' => $equipos->count(), 'etq' => 'Equipos disponibles (según filtros)', 'color' => 'success'],
    ['num' => $totalActivos, 'etq' => 'Equipos activos en total', 'color' => 'secondary'],
    ['num' => 'Q ' . number_format((float) $valorLibros, 2), 'etq' => 'Valor en libros de los disponibles', 'color' => 'info'],
]])

@if($imprimir)
    <div class="seccion-titulo">Listado de equipos disponibles</div>
@else
    <h2 class="h6 fw-bold text-uppercase text-secondary mb-2">Listado de equipos disponibles</h2>
@endif

<div class="{{ $imprimir ? '' : 'card' }}">
    <div class="{{ $imprimir ? '' : 'table-responsive' }}">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-dark">
                <tr>
                    <th>Código Contable</th>
                    <th>ST</th>
                    <th>Equipo</th>
                    <th>Marca</th>
                    <th>Modelo</th>
                    <th>Fecha de compra</th>
                    <th class="text-end">Valor en libros</th>
                </tr>
            </thead>
            <tbody>
                @forelse($equipos as $equipo)
                    <tr>
                        <td class="fw-semibold">{{ $equipo->codigo_contable }}</td>
                        <td>{{ $equipo->st ?? 'N/A' }}</td>
                        <td>{{ $equipo->tipoEquipo->nombre_tipo ?? 'N/A' }}</td>
                        <td>{{ $equipo->marca->nombre_marca ?? 'N/A' }}</td>
                        <td>{{ $equipo->modelo->nombre_modelo ?? 'N/A' }}</td>
                        <td>{{ $equipo->fecha_compra ? \Carbon\Carbon::parse($equipo->fecha_compra)->format('d/m/Y') : 'N/A' }}</td>
                        <td class="text-end">{{ $equipo->valor_libros !== null ? 'Q ' . number_format((float) $equipo->valor_libros, 2) : 'N/A' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">No hay equipos disponibles con esos filtros.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
