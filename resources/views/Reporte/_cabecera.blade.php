{{-- Encabezado de pantalla de un reporte: título y botón de impresión (usa los filtros activos) --}}
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2 fw-bold text-dark"><i class="bi {{ $icono }} me-2"></i>{{ $titulo }}</h1>
    <div class="d-flex gap-2">
        <a href="{{ route('reportes.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-grid me-1"></i> Todos los reportes
        </a>
        <a href="{{ route($ruta . '.imprimir', request()->query()) }}" target="_blank" class="btn btn-success btn-sm">
            <i class="bi bi-printer me-1"></i> Imprimir reporte
        </a>
    </div>
</div>
