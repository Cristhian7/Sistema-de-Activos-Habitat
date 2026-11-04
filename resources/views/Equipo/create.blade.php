@extends('layouts.app')

@section('title', 'Registrar Nuevo Equipo - Control de Activos')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2 fw-bold text-dark"><i class="bi bi-laptop me-2"></i>Registrar Nuevo Equipo</h1>
    <a href="{{ route('equipos.index') }}" class="btn btn-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Volver
    </a>
</div>

<form action="{{ route('equipos.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="row g-4">
        <!-- Información General -->
        <div class="col-md-12">
            <div class="card border-0 shadow-sm p-4">
                <h5 class="fw-bold text-primary mb-3"><i class="bi bi-info-circle me-2"></i>Información General</h5>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="codigo_contable" class="form-label fw-semibold">Código Contable *</label>
                        <input type="text" class="form-control" id="codigo_contable" name="codigo_contable" required value="{{ old('codigo_contable') }}">
                    </div>
                    <div class="col-md-4">
                        <label for="nombre_equipo" class="form-label fw-semibold">Nombre del Equipo *</label>
                        <input type="text" class="form-control" id="nombre_equipo" name="nombre_equipo" required value="{{ old('nombre_equipo') }}">
                    </div>
                    
                    <!-- Combobox Tipo de Equipo -->
                    <div class="col-md-4">
                        <label for="tipo_equipo_id_tipo_equipo" class="form-label fw-semibold">Tipo de Equipo *</label>
                        <select class="form-select" id="tipo_equipo_id_tipo_equipo" name="tipo_equipo_id_tipo_equipo" required>
                            <option value="">Seleccione un tipo...</option>
                            @foreach($tipos as $tipo)
                                <option value="{{ $tipo->id_tipo_equipo }}">{{ $tipo->nombre_tipo }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Combobox Marca -->
                    <div class="col-md-4">
                        <label for="marca_id_marca" class="form-label fw-semibold">Marca *</label>
                        <select class="form-select" id="marca_id_marca" name="marca_id_marca" required>
                            <option value="">Seleccione una marca...</option>
                            @foreach($marcas as $marca)
                                <option value="{{ $marca->id_marca }}">{{ $marca->nombre_marca }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Combobox Modelo -->
                    <div class="col-md-4">
                        <label for="modelo_id_modelo" class="form-label fw-semibold">Modelo *</label>
                        <select class="form-select" id="modelo_id_modelo" name="modelo_id_modelo" required>
                            <option value="">Seleccione un modelo...</option>
                            @foreach($modelos as $modelo)
                                <option value="{{ $modelo->id_modelo }}">{{ $modelo->nombre_modelo }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Combobox Estado (Por defecto ID 1 - Activo) -->
                    <div class="col-md-4">
                        <label for="estado_id_estado" class="form-label fw-semibold">Estado *</label>
                        <select class="form-select" id="estado_id_estado" name="estado_id_estado" required>
                            @foreach($estados as $estado)
                                <option value="{{ $estado->id_estado }}" {{ $estado->id_estado == 1 ? 'selected' : '' }}>
                                    {{ $estado->nombre_estado }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Especificaciones de Hardware -->
        <div class="col-md-12">
            <div class="card border-0 shadow-sm p-4">
                <h5 class="fw-bold text-primary mb-3"><i class="bi bi-cpu me-2"></i>Especificaciones de Hardware</h5>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="procesador" class="form-label fw-semibold">Procesador</label>
                        <input type="text" class="form-control" id="procesador" name="procesador" placeholder="Ej. Core I5 , I7">
                    </div>
                    <div class="col-md-4">
                        <label for="ram" class="form-label fw-semibold">Memoria RAM</label>
                        <input type="text" class="form-control" id="ram" name="ram" placeholder="Ej. 16GB">
                    </div>
                    <div class="col-md-4">
                        <label for="tipo_ram" class="form-label fw-semibold">Tipo de RAM</label>
                        <input type="text" class="form-control" id="tipo_ram" name="tipo_ram" placeholder="Ej. DDR4 / DDR5">
                    </div>
                    <div class="col-md-3">
                        <label for="ssd" class="form-label fw-semibold">SSD</label>
                        <input type="text" class="form-control" id="ssd" name="ssd" placeholder="Ej. 512GB NVMe">
                    </div>
                    <div class="col-md-3">
                        <label for="st" class="form-label fw-semibold">Service Tag</label>
                        <input type="text" class="form-control" id="st" name="st" placeholder="Esta en la parte de abajo del equipo">
                    </div>
                    <div class="col-md-6">
                        <label for="ex" class="form-label fw-semibold">Express Code</label>
                        <input type="text" class="form-control" id="ex" name="ex" placeholder="Esta en la parte de abajo del equipo">
                    </div>
                </div>
            </div>
        </div>

        <!-- Datos Financieros y Notas -->
        <div class="col-md-12">
            <div class="card border-0 shadow-sm p-4">
                <h5 class="fw-bold text-primary mb-3"><i class="bi bi-cash-stack me-2"></i>Datos Financieros y Notas</h5>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="fecha_compra" class="form-label fw-semibold">Fecha de Compra</label>
                        <input type="date" class="form-control" id="fecha_compra" name="fecha_compra">
                    </div>
                    <div class="col-md-4">
                        <label for="precio_compra" class="form-label fw-semibold">Precio de Compra</label>
                        <input type="number" step="0.01" class="form-control" id="precio_compra" name="precio_compra" placeholder="0.00">
                    </div>
                    <div class="col-md-4">
                        <label for="valor_libros" class="form-label fw-semibold">Valor en Libros</label>
                        <input type="number" step="0.01" class="form-control" id="valor_libros" name="valor_libros" placeholder="0.00">
                    </div>
                    <div class="col-md-12">
                        <label for="notas_adicionales" class="form-label fw-semibold">Notas Adicionales</label>
                        <textarea class="form-control" id="notas_adicionales" name="notas_adicionales" rows="2" placeholder="Observaciones del equipo..."></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Imágenes del Equipo -->
        <div class="col-md-12">
            <div class="card border-0 shadow-sm p-4">
                <h5 class="fw-bold text-primary mb-3"><i class="bi bi-images me-2"></i>Imágenes y Códigos</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="imagen_equipo" class="form-label fw-semibold">Fotografía del Equipo</label>
                        <input type="file" class="form-control" id="imagen_equipo" name="imagen_equipo" accept="image/*">
                        <div class="form-text">Sube una foto real del equipo físico.</div>
                    </div>
                    <div class="col-md-6">
                        <label for="imagen_codigo" class="form-label fw-semibold">Fotografía del Código Contable</label>
                        <input type="file" class="form-control" id="imagen_codigo" name="imagen_codigo" accept="image/*">
                        <div class="form-text">Sube la foto del código de barras o etiqueta contable.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4 mb-5 text-end">
        <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold">
            <i class="bi bi-save me-1"></i> Guardar Equipo
        </button>
    </div>
</form>
@endsection