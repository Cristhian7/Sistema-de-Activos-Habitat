@extends('layouts.app')

@section('title', 'Nueva Baja - Control de Activos')

@section('content')

@vite(['resources/js/buscar-equipo-baja.js'])

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2 fw-bold text-dark"><i class="bi bi-trash me-2"></i>Registrar Baja de Equipo</h1>
    <a href="{{ route('bajas.index') }}" class="btn btn-secondary btn-sm">
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

<form action="{{ route('bajas.store') }}" method="POST" novalidate
      onsubmit="return confirm('¿Confirma dar de baja este equipo? Su estado cambiará a Baja y ya no se podrá reasignar.');">
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
                        <label for="b_nombre_equipo" class="form-label fw-semibold">Nombre del Equipo</label>
                        <input type="text" class="form-control bg-light" id="b_nombre_equipo" readonly>
                    </div>

                    <div class="col-md-4">
                        <label for="b_tipo" class="form-label fw-semibold">Tipo de Equipo</label>
                        <input type="text" class="form-control bg-light" id="b_tipo" readonly>
                    </div>

                    <div class="col-md-4">
                        <label for="b_marca" class="form-label fw-semibold">Marca</label>
                        <input type="text" class="form-control bg-light" id="b_marca" readonly>
                    </div>

                    <div class="col-md-4">
                        <label for="b_modelo" class="form-label fw-semibold">Modelo</label>
                        <input type="text" class="form-control bg-light" id="b_modelo" readonly>
                    </div>

                    <div class="col-md-4">
                        <label for="b_estado" class="form-label fw-semibold">Estado Actual</label>
                        <input type="text" class="form-control bg-light" id="b_estado" readonly>
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
                        <label for="b_procesador" class="form-label fw-semibold">Procesador</label>
                        <input type="text" class="form-control bg-light" id="b_procesador" readonly>
                    </div>
                    <div class="col-md-4">
                        <label for="b_ram" class="form-label fw-semibold">Memoria RAM</label>
                        <input type="text" class="form-control bg-light" id="b_ram" readonly>
                    </div>
                    <div class="col-md-4">
                        <label for="b_tipo_ram" class="form-label fw-semibold">Tipo de RAM</label>
                        <input type="text" class="form-control bg-light" id="b_tipo_ram" readonly>
                    </div>
                    <div class="col-md-3">
                        <label for="b_ssd" class="form-label fw-semibold">SSD</label>
                        <input type="text" class="form-control bg-light" id="b_ssd" readonly>
                    </div>
                    <div class="col-md-3">
                        <label for="b_st" class="form-label fw-semibold">Service Tag</label>
                        <input type="text" class="form-control bg-light" id="b_st" readonly>
                    </div>
                    <div class="col-md-6">
                        <label for="b_ex" class="form-label fw-semibold">Express Code</label>
                        <input type="text" class="form-control bg-light" id="b_ex" readonly>
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
                        <label for="b_fecha_compra" class="form-label fw-semibold">Fecha de Compra</label>
                        <input type="text" class="form-control bg-light" id="b_fecha_compra" readonly>
                    </div>
                    <div class="col-md-4">
                        <label for="b_precio_compra" class="form-label fw-semibold">Precio de Compra</label>
                        <input type="text" class="form-control bg-light" id="b_precio_compra" readonly>
                    </div>
                    <div class="col-md-4">
                        <label for="b_valor_libros" class="form-label fw-semibold">Valor en Libros</label>
                        <input type="text" class="form-control bg-light" id="b_valor_libros" readonly>
                    </div>
                    <div class="col-md-12">
                        <label for="b_notas" class="form-label fw-semibold">Notas del Equipo</label>
                        <textarea class="form-control bg-light" id="b_notas" rows="2" readonly></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Imágenes y Códigos -->
        <div class="col-md-12">
            <div class="card border-0 shadow-sm p-4">
                <h5 class="fw-bold text-primary mb-3"><i class="bi bi-images me-2"></i>Imágenes y Códigos</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Fotografía del Equipo</label>
                        <div id="b_wrap_img_equipo" class="mb-2 text-center p-2 border rounded bg-light d-none">
                            <img id="b_img_equipo" src="" alt="Fotografía del Equipo"
                                 class="img-fluid rounded"
                                 style="width: 100%; max-height: 350px; object-fit: contain;">
                            <div class="small text-muted mt-1">Imagen actual cargada</div>
                        </div>
                        <div id="b_wrap_img_equipo_vacio"><span class="badge bg-secondary">Se mostrará al buscar el equipo</span></div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Fotografía del Código Contable</label>
                        <div id="b_wrap_img_codigo" class="mb-2 text-center p-2 border rounded bg-light d-none">
                            <img id="b_img_codigo" src="" alt="Fotografía del Código Contable"
                                 class="img-fluid rounded"
                                 style="max-height: 150px; object-fit: contain;">
                            <div class="small text-muted mt-1">Imagen actual cargada</div>
                        </div>
                        <div id="b_wrap_img_codigo_vacio"><span class="badge bg-secondary">Se mostrará al buscar el equipo</span></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Empleado con el equipo asignado (se carga solo desde el traslado del equipo) -->
        <div class="col-md-12">
            <div class="card border-0 shadow-sm p-4">
                <h5 class="fw-bold text-primary mb-3"><i class="bi bi-person me-2"></i>Empleado Asignado</h5>

                <div id="b_emp_nota" class="alert alert-info d-none">
                    <i class="bi bi-info-circle-fill me-1"></i>
                    Este equipo ya fue devuelto y está disponible. La baja se registrará con su último usuario.
                </div>

                <div id="b_emp_aviso" class="alert alert-warning d-none">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i>
                    Este equipo no está asignado a ningún empleado, por lo que no se puede registrar su baja. Registre primero su traslado.
                </div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="b_emp_cod_l4" class="form-label fw-semibold">Código L4</label>
                        <input type="text" class="form-control bg-light" id="b_emp_cod_l4" readonly>
                    </div>
                    <div class="col-md-4">
                        <label for="b_emp_nombre" class="form-label fw-semibold">Nombre</label>
                        <input type="text" class="form-control bg-light" id="b_emp_nombre" readonly>
                    </div>
                    <div class="col-md-4">
                        <label for="b_emp_apellido" class="form-label fw-semibold">Apellido</label>
                        <input type="text" class="form-control bg-light" id="b_emp_apellido" readonly>
                    </div>
                    <div class="col-md-6">
                        <label for="b_emp_puesto" class="form-label fw-semibold">Puesto</label>
                        <input type="text" class="form-control bg-light" id="b_emp_puesto" readonly>
                    </div>
                    <div class="col-md-6">
                        <label for="b_emp_afiliado" class="form-label fw-semibold">Afiliado</label>
                        <input type="text" class="form-control bg-light" id="b_emp_afiliado" readonly>
                    </div>
                </div>
            </div>
        </div>

        <!-- Datos de la Baja -->
        <div class="col-md-12">
            <div class="card border-0 shadow-sm p-4">
                <h5 class="fw-bold text-danger mb-3"><i class="bi bi-trash me-2"></i>Datos de la Baja</h5>
                <div class="row g-3">

                    <div class="col-md-4">
                        <label for="fecha_baja" class="form-label fw-semibold">Fecha de Baja <span class="text-danger">*</span></label>
                        <input type="date"
                            class="form-control @error('fecha_baja') is-invalid @enderror"
                            id="fecha_baja"
                            name="fecha_baja"
                            value="{{ old('fecha_baja', date('Y-m-d')) }}">
                        @error('fecha_baja')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-8">
                        <label for="problema" class="form-label fw-semibold">Problema del Equipo <span class="text-danger">*</span></label>
                        <input type="text"
                            class="form-control @error('problema') is-invalid @enderror"
                            id="problema"
                            name="problema"
                            maxlength="100"
                            value="{{ old('problema') }}"
                            placeholder="Ej. Falla de placa madre">
                        @error('problema')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label for="b_anos_uso" class="form-label fw-semibold">Años de Uso</label>
                        <!-- Sin name: no se envía; el servidor lo calcula con la fecha de compra -->
                        <input type="text" class="form-control bg-light" id="b_anos_uso" readonly placeholder="Se calcula solo">
                        <div class="form-text">Fecha de compra vs. fecha actual.</div>
                    </div>

                    <div class="col-md-3">
                        <label for="b_mes_uso" class="form-label fw-semibold">Meses de Uso</label>
                        <!-- Sin name: no se envía; el servidor lo calcula con la fecha de compra -->
                        <input type="text" class="form-control bg-light" id="b_mes_uso" readonly placeholder="Se calcula solo">
                        <div class="form-text">Meses después de los años completos.</div>
                    </div>

                    <div class="col-md-6">
                        <label for="necesita_reemplazo" class="form-label fw-semibold">¿Necesita Reemplazo? <span class="text-danger">*</span></label>
                        <select class="form-select @error('necesita_reemplazo') is-invalid @enderror" id="necesita_reemplazo" name="necesita_reemplazo">
                            <option value="">Seleccione...</option>
                            <option value="SI" {{ old('necesita_reemplazo') === 'SI' ? 'selected' : '' }}>Sí</option>
                            <option value="NO" {{ old('necesita_reemplazo') === 'NO' ? 'selected' : '' }}>No</option>
                        </select>
                        @error('necesita_reemplazo')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                        <button type="button" id="btnGenerarEquipo" class="btn btn-outline-primary btn-sm mt-2" disabled>
                            <i class="bi bi-plus-circle me-1"></i> Generar equipo
                        </button>
                    </div>

                    <div class="col-md-12">
                        <label for="detalle" class="form-label fw-semibold">Detalle / Motivo de la Baja <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('detalle') is-invalid @enderror"
                            id="detalle"
                            name="detalle"
                            rows="3"
                            maxlength="1000"
                            placeholder="Describa el motivo por el cual se da de baja el equipo...">{{ old('detalle') }}</textarea>
                        @error('detalle')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Solicitud de equipo nuevo (se muestra con el botón "Generar equipo") -->
        <div class="col-md-12 d-none" id="panelSolicitud">
            <div class="card border-0 shadow-sm p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold text-primary mb-0"><i class="bi bi-box-seam me-2"></i>Solicitud de Equipo Nuevo</h5>
                    <!-- Imprime únicamente esta solicitud, no el formulario completo -->
                    <button type="button" id="btnImprimirSolicitud" class="btn btn-success btn-sm"
                            data-logo="{{ asset('img/logo-habitat.png') }}">
                        <i class="bi bi-printer me-1"></i> Imprimir
                    </button>
                </div>
                <div class="table-responsive">
                    <table id="tablaSolicitud" class="table table-bordered align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>Código Contable</th>
                                <th>Fecha de Compra</th>
                                <th>Marca</th>
                                <th>Modelo</th>
                                <th>Afiliado</th>
                                <th>Precio de Compra</th>
                                <th>Valor en Libros</th>
                                <th>Tipo de Equipo</th>
                                <th>Equipo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td id="s_codigo" class="fw-semibold"></td>
                                <td id="s_fecha_compra"></td>
                                <td id="s_marca"></td>
                                <td id="s_modelo"></td>
                                <td id="s_afiliado"></td>
                                <td id="s_precio"></td>
                                <td id="s_valor"></td>
                                <td id="s_tipo"></td>
                                <td id="s_equipo" class="fw-semibold text-primary"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4 mb-5 text-end">
        <button type="submit" class="btn btn-danger px-4 py-2 fw-semibold">
            <i class="bi bi-save me-1"></i> Guardar Baja
        </button>
    </div>
</form>

@endsection
