@extends($imprimir ? 'Reporte.print-layout' : 'layouts.app')

@section('title', 'Reporte de Bajas de Equipos - Control de Activos')

@section('content')

@unless($imprimir)
    @include('Reporte._cabecera', ['icono' => 'bi-trash', 'ruta' => 'reportes.bajas-equipos'])

    <!-- Filtros -->
    <form method="GET" action="{{ route('reportes.bajas-equipos') }}" class="card mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="codigo" class="form-label small fw-semibold mb-1">Código contable</label>
                    <input type="text" id="codigo" name="codigo" class="form-control" value="{{ $f['codigo'] }}" placeholder="Ej. CE-04728 o 4728">
                </div>
                <div class="col-md-2">
                    <label for="st" class="form-label small fw-semibold mb-1">ST</label>
                    <input type="text" id="st" name="st" class="form-control" value="{{ $f['st'] }}" placeholder="Service tag">
                </div>
                <div class="col-md-2">
                    <label for="anio" class="form-label small fw-semibold mb-1">Año de baja</label>
                    <select id="anio" name="anio" class="form-select">
                        <option value="">Todos</option>
                        @foreach($anios as $anio)
                            <option value="{{ $anio }}" @selected($f['anio'] == $anio)>{{ $anio }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="mes" class="form-label small fw-semibold mb-1">Mes de baja</label>
                    <select id="mes" name="mes" class="form-select">
                        <option value="">Todos</option>
                        @foreach($meses as $numero => $nombre)
                            <option value="{{ $numero }}" @selected($f['mes'] == $numero)>{{ $nombre }}</option>
                        @endforeach
                    </select>
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
                <div class="col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-search me-1"></i> Buscar</button>
                    <a href="{{ route('reportes.bajas-equipos') }}" class="btn btn-outline-secondary btn-sm">Limpiar</a>
                </div>
            </div>
        </div>
    </form>
@endunless

@include('Reporte._kpis', ['kpis' => [
    ['num' => $bajas->count(), 'etq' => 'Equipos dados de baja (según filtros)', 'color' => 'danger'],
    ['num' => $totalGeneral, 'etq' => 'Total de bajas registradas', 'color' => 'secondary'],
    ['num' => $conReemplazo, 'etq' => 'Con reemplazo solicitado', 'color' => 'warning'],
]])

<div class="{{ $imprimir ? 'resumenes' : 'row g-3 mb-4' }}">
    <div class="{{ $imprimir ? '' : 'col-md-5' }}">
        @include('Reporte._resumen', ['titulo' => 'Bajas por afiliado', 'columna' => 'Afiliado', 'filas' => $porAfiliado])
    </div>
    @if($imprimir)<div></div>@endif
</div>

@if($imprimir)
    <div class="seccion-titulo">Listado de bajas</div>
@else
    <h2 class="h6 fw-bold text-uppercase text-secondary mb-2">Listado de bajas</h2>
@endif

<div class="{{ $imprimir ? '' : 'card' }}">
    <div class="{{ $imprimir ? '' : 'table-responsive' }}">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-dark">
                <tr>
                    <th>Código Contable</th>
                    <th>ST</th>
                    <th>Equipo</th>
                    <th>Marca / Modelo</th>
                    <th>Último empleado</th>
                    <th>Afiliado</th>
                    <th>Problema</th>
                    <th>Tiempo de uso</th>
                    <th>Reemplazo</th>
                    <th>Fecha de baja</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bajas as $baja)
                    <tr>
                        <td class="fw-semibold">{{ $baja->equipo->codigo_contable ?? 'N/A' }}</td>
                        <td>{{ $baja->equipo->st ?? 'N/A' }}</td>
                        <td>{{ $baja->equipo->tipoEquipo->nombre_tipo ?? 'N/A' }}</td>
                        <td>{{ $baja->equipo->marca->nombre_marca ?? 'N/A' }} {{ $baja->equipo->modelo->nombre_modelo ?? '' }}</td>
                        <td>{{ trim(($baja->empleado->nombre_empleado ?? '') . ' ' . ($baja->empleado->apellido_empleado ?? '')) ?: 'N/A' }}</td>
                        <td>{{ $baja->empleado->afiliado->nombre_afiliado ?? 'N/A' }}</td>
                        <td>{{ $imprimir ? $baja->problema : \Illuminate\Support\Str::limit($baja->problema, 40) }}</td>
                        <td>{{ $baja->tiempo_uso }}</td>
                        <td>{{ $baja->necesita_reemplazo === 'SI' ? 'Sí' : ($baja->necesita_reemplazo === 'NO' ? 'No' : 'N/A') }}</td>
                        <td>{{ $baja->fecha_baja ? \Carbon\Carbon::parse($baja->fecha_baja)->format('d/m/Y') : 'N/A' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center text-muted py-4">No hay bajas con esos filtros.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
