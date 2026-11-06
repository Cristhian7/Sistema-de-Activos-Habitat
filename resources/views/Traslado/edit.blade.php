@extends('layouts.app')

@section('title', 'Editar Traslado - Control de Activos')

@section('content')

@vite(['resources/js/buscar-equipo-tras.js', 'resources/js/buscar-codl4.js'])

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2 fw-bold text-dark"><i class="bi bi-pencil-square me-2"></i>Editar Traslado</h1>
    <a href="{{ route('traslados.index') }}" class="btn btn-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Volver al Listado
    </a>
</div>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('traslados.update', $traslado->id_traslado) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="row mb-3">
        <!-- Búsqueda por Código de Equipo -->
        <div class="col-md-6">
            <label for="codigo_equipo" class="form-label fw-semibold">Código de Equipo <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('codigo_equipo') is-invalid @enderror"
                   id="codigo_equipo" name="codigo_equipo"
                   placeholder="Ingrese código y presione fuera..."
                   value="{{ old('codigo_equipo', $traslado->equipo->codigo_contable ?? '') }}" required>

            <!-- Tarjeta de información completa del equipo -->
            <div id="infoEquipo" class="mt-2 p-3 bg-light border rounded small shadow-sm" style="display: none;">
                <span class="d-block text-success fw-bold mb-2"><i class="bi bi-check-circle me-1"></i>Equipo encontrado:</span>
                <div class="row">
                    <div class="col-md-6">
                        <div><strong>Nombre:</strong> <span id="lblNombreEquipo"></span></div>
                        <div><strong>Tipo:</strong> <span id="lblTipoEquipo"></span></div>
                        <div><strong>Marca:</strong> <span id="lblMarcaEquipo"></span></div>
                        <div><strong>Modelo:</strong> <span id="lblModeloEquipo"></span></div>
                        <div><strong>Procesador:</strong> <span id="lblProcesador"></span></div>
                        <div><strong>RAM:</strong> <span id="lblRam"></span></div>
                        <div><strong>SSD:</strong> <span id="lblSsd"></span></div>
                    </div>
                    <div class="col-md-6">
                        <div><strong>ST:</strong> <span id="lblSt"></span></div>
                        <div><strong>EX:</strong> <span id="lblEx"></span></div>
                        <div><strong>Fecha Compra:</strong> <span id="lblFechaCompra"></span></div>
                        <div><strong>Precio Compra:</strong> <span id="lblPrecioCompra"></span></div>
                        <div><strong>Valor Libros:</strong> <span id="lblBalorLibros"></span></div>
                        <div><strong>Estado:</strong> <span id="lblEstado"></span></div>
                        <div><strong>Notas:</strong> <span id="lblNotas"></span></div>
                    </div>
                </div>

                <!-- Imágenes del equipo -->
                <div class="row mt-2">
                    <div class="col-md-6">
                        <div id="wrapImgEquipo" class="text-center p-2 border rounded bg-white d-none">
                            <img id="imgEquipo" src="" alt="Fotografía del Equipo"
                                 class="img-fluid rounded"
                                 style="width: 100%; max-height: 350px; object-fit: contain;">
                            <div class="small text-muted mt-1">Fotografía del equipo</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div id="wrapImgCodigo" class="text-center p-2 border rounded bg-white d-none">
                            <img id="imgCodigo" src="" alt="Fotografía del Código Contable"
                                 class="img-fluid rounded"
                                 style="max-height: 150px; object-fit: contain;">
                            <div class="small text-muted mt-1">Fotografía del código contable</div>
                        </div>
                    </div>
                </div>
            </div>

            @error('codigo_equipo')
            <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <!-- Búsqueda por Código L4 del Empleado -->
        <div class="col-md-6">
            <label for="cod_l4" class="form-label fw-semibold">Código L4 Empleado <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('cod_l4') is-invalid @enderror"
                   id="cod_l4" name="cod_l4"
                   placeholder="Ingrese código L4 y presione fuera..."
                   value="{{ old('cod_l4', $traslado->empleado->cod_l4 ?? '') }}" required>

            <!-- Tarjeta de información del empleado encontrada -->
            <div id="infoEmpleado" class="mt-2 p-3 bg-light border rounded small shadow-sm" style="display: none;">
                <span class="d-block text-success fw-bold mb-2"><i class="bi bi-check-circle me-1"></i>Empleado encontrado:</span>
                <div><strong>Nombre:</strong> <span id="lblNombreEmpleado"></span></div>
                <div><strong>Puesto:</strong> <span id="lblPuesto"></span></div>
                <div><strong>Afiliado:</strong> <span id="lblAfiliado"></span></div>
            </div>

            @error('cod_l4')
            <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <label for="fecha_envio" class="form-label fw-semibold">Fecha de Envío <span class="text-danger">*</span></label>
            <input type="date" class="form-control @error('fecha_envio') is-invalid @enderror" id="fecha_envio" name="fecha_envio" value="{{ old('fecha_envio', \Carbon\Carbon::parse($traslado->fecha_envio)->format('Y-m-d')) }}" required>
            @error('fecha_envio')
            <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-md-6">
            <label for="motivo_traslado" class="form-label fw-semibold">Motivo del Traslado</label>
            <input type="text" class="form-control @error('motivo_traslado') is-invalid @enderror" id="motivo_traslado" name="motivo_traslado" value="{{ old('motivo_traslado', $traslado->motivo_traslado) }}">
            @error('motivo_traslado')
            <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="mb-3">
        <label for="descripcion" class="form-label fw-semibold">Descripción / Observaciones</label>
        <textarea class="form-control @error('descripcion') is-invalid @enderror" id="descripcion" name="descripcion" rows="3">{{ old('descripcion', $traslado->descripcion) }}</textarea>
        @error('descripcion')
        <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="mt-4 mb-5 text-end">
        <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold">
            <i class="bi bi-save me-1"></i> Actualizar Traslado
        </button>
    </div>
</form>

@endsection
