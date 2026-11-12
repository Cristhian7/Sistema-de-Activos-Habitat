@extends('layouts.app')

@section('title', 'Nuevo Mantenimiento - Control de Activos')

@section('content')

@vite(['resources/js/buscar-equipo-mant.js', 'resources/js/buscar-codl4.js'])

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2 fw-bold text-dark"><i class="bi bi-tools me-2"></i>Registrar Nuevo Mantenimiento</h1>
    <a href="{{ route('mantenimientos.index') }}" class="btn btn-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Volver
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

<form action="{{ route('mantenimientos.store') }}" method="POST" enctype="multipart/form-data" novalidate>
    @csrf

    <div class="row g-4">
        <!-- Información General -->
        <div class="col-md-12">
            <div class="card border-0 shadow-sm p-4">
                <h5 class="fw-bold text-primary mb-3"><i class="bi bi-info-circle me-2"></i>Información General</h5>
                <div class="row g-3">

                    <!-- Código del equipo (completo o últimos 4 dígitos) -->
                    <div class="col-md-4">
                        <label for="codigo_equipo" class="form-label fw-semibold">Código Contable <span class="text-danger">*</span></label>
                        <input type="text"
                            class="form-control @error('codigo_equipo') is-invalid @enderror"
                            id="codigo_equipo"
                            name="codigo_equipo"
                            value="{{ old('codigo_equipo') }}"
                            placeholder="Ej. CE-04728 o 4728">
                        <div class="form-text">Escriba el código completo o solo los últimos 4 dígitos.</div>
                        @error('codigo_equipo')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="m_nombre_equipo" class="form-label fw-semibold">Nombre del Equipo</label>
                        <input type="text" class="form-control bg-light" id="m_nombre_equipo" readonly>
                    </div>

                    <div class="col-md-4">
                        <label for="m_tipo" class="form-label fw-semibold">Tipo de Equipo</label>
                        <input type="text" class="form-control bg-light" id="m_tipo" readonly>
                    </div>

                    <div class="col-md-4">
                        <label for="m_marca" class="form-label fw-semibold">Marca</label>
                        <input type="text" class="form-control bg-light" id="m_marca" readonly>
                    </div>

                    <div class="col-md-4">
                        <label for="m_modelo" class="form-label fw-semibold">Modelo</label>
                        <input type="text" class="form-control bg-light" id="m_modelo" readonly>
                    </div>

                    <div class="col-md-4">
                        <label for="m_estado" class="form-label fw-semibold">Estado</label>
                        <input type="text" class="form-control bg-light" id="m_estado" readonly>
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
                        <label for="m_procesador" class="form-label fw-semibold">Procesador</label>
                        <input type="text" class="form-control bg-light" id="m_procesador" readonly>
                    </div>
                    <div class="col-md-4">
                        <label for="m_ram" class="form-label fw-semibold">Memoria RAM</label>
                        <input type="text" class="form-control bg-light" id="m_ram" readonly>
                    </div>
                    <div class="col-md-4">
                        <label for="m_tipo_ram" class="form-label fw-semibold">Tipo de RAM</label>
                        <input type="text" class="form-control bg-light" id="m_tipo_ram" readonly>
                    </div>
                    <div class="col-md-3">
                        <label for="m_ssd" class="form-label fw-semibold">SSD</label>
                        <input type="text" class="form-control bg-light" id="m_ssd" readonly>
                    </div>
                    <div class="col-md-3">
                        <label for="m_st" class="form-label fw-semibold">Service Tag</label>
                        <input type="text" class="form-control bg-light" id="m_st" readonly>
                    </div>
                    <div class="col-md-6">
                        <label for="m_ex" class="form-label fw-semibold">Express Code</label>
                        <input type="text" class="form-control bg-light" id="m_ex" readonly>
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
                        <label for="m_fecha_compra" class="form-label fw-semibold">Fecha de Compra</label>
                        <input type="text" class="form-control bg-light" id="m_fecha_compra" readonly>
                    </div>
                    <div class="col-md-4">
                        <label for="m_precio_compra" class="form-label fw-semibold">Precio de Compra</label>
                        <input type="text" class="form-control bg-light" id="m_precio_compra" readonly>
                    </div>
                    <div class="col-md-4">
                        <label for="m_valor_libros" class="form-label fw-semibold">Valor en Libros</label>
                        <input type="text" class="form-control bg-light" id="m_valor_libros" readonly>
                    </div>
                    <div class="col-md-12">
                        <label for="m_notas" class="form-label fw-semibold">Notas del Equipo</label>
                        <textarea class="form-control bg-light" id="m_notas" rows="2" readonly></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fotografías del mantenimiento (independientes de las del equipo) -->
        <div class="col-md-12">
            <div class="card border-0 shadow-sm p-4">
                <h5 class="fw-bold text-primary mb-1"><i class="bi bi-images me-2"></i>Fotografías del Mantenimiento</h5>
                <p class="text-muted small mb-3">Opcional. Suba fotos del componente que se cambió o mejoró (disco duro, memoria RAM, teclado, pantalla, etc.). No reemplazan las imágenes del equipo.</p>
                <div class="row g-3">

                    <!-- Fotografía 1 -->
                    <div class="col-md-6">
                        <label for="imagen_mantenimiento_1" class="form-label fw-semibold">Fotografía 1 del Componente</label>
                        <input type="hidden" name="temp_imagen_mant_1" value="{{ old('temp_imagen_mant_1') }}">

                        <div id="preview_mant_1_wrapper" class="mb-2 text-center p-2 border rounded bg-light {{ old('temp_imagen_mant_1') ? '' : 'd-none' }}">
                            <img id="preview_mant_1" src="{{ old('temp_imagen_mant_1') ? 'data:image/jpeg;base64,' . old('temp_imagen_mant_1') : '' }}" alt="Vista previa fotografía 1"
                                 class="img-fluid rounded"
                                 style="width: 100%; max-height: 350px; object-fit: contain;">
                            <div class="small text-muted mt-1">Vista previa</div>
                        </div>

                        <input type="file" class="form-control @error('imagen_mantenimiento_1') is-invalid @enderror" id="imagen_mantenimiento_1" name="imagen_mantenimiento_1" accept="image/*">
                        @error('imagen_mantenimiento_1')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Fotografía 2 -->
                    <div class="col-md-6">
                        <label for="imagen_mantenimiento_2" class="form-label fw-semibold">Fotografía 2 del Componente</label>
                        <input type="hidden" name="temp_imagen_mant_2" value="{{ old('temp_imagen_mant_2') }}">

                        <div id="preview_mant_2_wrapper" class="mb-2 text-center p-2 border rounded bg-light {{ old('temp_imagen_mant_2') ? '' : 'd-none' }}">
                            <img id="preview_mant_2" src="{{ old('temp_imagen_mant_2') ? 'data:image/jpeg;base64,' . old('temp_imagen_mant_2') : '' }}" alt="Vista previa fotografía 2"
                                 class="img-fluid rounded"
                                 style="width: 100%; max-height: 350px; object-fit: contain;">
                            <div class="small text-muted mt-1">Vista previa</div>
                        </div>

                        <input type="file" class="form-control @error('imagen_mantenimiento_2') is-invalid @enderror" id="imagen_mantenimiento_2" name="imagen_mantenimiento_2" accept="image/*">
                        @error('imagen_mantenimiento_2')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                </div>
            </div>
        </div>

        <!-- Datos del Mantenimiento -->
        <div class="col-md-12">
            <div class="card border-0 shadow-sm p-4">
                <h5 class="fw-bold text-primary mb-3"><i class="bi bi-tools me-2"></i>Datos del Mantenimiento</h5>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="cod_l4" class="form-label fw-semibold">Código L4 Empleado Responsable <span class="text-danger">*</span></label>
                        <input type="text"
                            class="form-control @error('cod_l4') is-invalid @enderror"
                            id="cod_l4"
                            name="cod_l4"
                            value="{{ old('cod_l4') }}"
                            placeholder="Ingrese código L4">
                        @error('cod_l4')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="fecha_mantenimiento" class="form-label fw-semibold">Fecha de Mantenimiento <span class="text-danger">*</span></label>
                        <input type="date"
                            class="form-control @error('fecha_mantenimiento') is-invalid @enderror"
                            id="fecha_mantenimiento"
                            name="fecha_mantenimiento"
                            value="{{ old('fecha_mantenimiento', date('Y-m-d')) }}">
                        @error('fecha_mantenimiento')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Tarjeta del empleado encontrado (la llena buscar-codl4.js) -->
                    <div class="col-md-4">
                        <div id="infoEmpleado" class="p-3 bg-light border rounded small shadow-sm" style="display: none;">
                            <span class="d-block text-success fw-bold mb-2"><i class="bi bi-check-circle me-1"></i>Empleado encontrado:</span>
                            <div><strong>Nombre:</strong> <span id="lblNombreEmpleado"></span></div>
                            <div><strong>Puesto:</strong> <span id="lblPuesto"></span></div>
                            <div><strong>Afiliado:</strong> <span id="lblAfiliado"></span></div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <label for="descripcion" class="form-label fw-semibold">Descripción del Mantenimiento <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('descripcion') is-invalid @enderror"
                            id="descripcion"
                            name="descripcion"
                            rows="3"
                            placeholder="Detalle del mantenimiento realizado...">{{ old('descripcion') }}</textarea>
                        @error('descripcion')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4 mb-5 text-end">
        <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold">
            <i class="bi bi-save me-1"></i> Guardar Mantenimiento
        </button>
    </div>
</form>

<!-- Vista previa instantánea de las fotos del mantenimiento -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    function setupPreview(inputId, wrapperId, imgId, hiddenName) {
        const input = document.getElementById(inputId);
        const wrapper = document.getElementById(wrapperId);
        const img = document.getElementById(imgId);
        const hidden = document.querySelector(`input[name="${hiddenName}"]`);
        if (!input) return;

        input.addEventListener('change', function (event) {
            const file = event.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function (e) {
                img.src = e.target.result;
                wrapper.classList.remove('d-none');
                // Un archivo nuevo reemplaza el respaldo temporal anterior
                if (hidden) hidden.value = '';
            };
            reader.readAsDataURL(file);
        });
    }

    setupPreview('imagen_mantenimiento_1', 'preview_mant_1_wrapper', 'preview_mant_1', 'temp_imagen_mant_1');
    setupPreview('imagen_mantenimiento_2', 'preview_mant_2_wrapper', 'preview_mant_2', 'temp_imagen_mant_2');
});
</script>

@endsection
