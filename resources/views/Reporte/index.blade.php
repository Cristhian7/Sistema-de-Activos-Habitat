@extends('layouts.app')

@section('title', 'Reportería - Control de Activos')

@section('content')

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2 fw-bold text-dark"><i class="bi bi-bar-chart-line me-2"></i>Reportería</h1>
</div>

<p class="text-muted">Elige un reporte. Todos se pueden filtrar y luego imprimir con los filtros aplicados.</p>

<div class="row g-3">
    @foreach($reportes as $reporte)
        <div class="col-md-6 col-xl-4">
            <a href="{{ route($reporte['ruta']) }}" class="text-decoration-none">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <span class="fs-3 text-primary me-3"><i class="bi {{ $reporte['icono'] }}"></i></span>
                            <h2 class="h5 fw-bold text-dark mb-0">{{ $reporte['titulo'] }}</h2>
                        </div>
                        <p class="text-muted small mb-0">{{ $reporte['descripcion'] }}</p>
                    </div>
                </div>
            </a>
        </div>
    @endforeach
</div>
@endsection
